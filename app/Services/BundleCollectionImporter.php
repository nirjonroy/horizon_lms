<?php

namespace App\Services;

use App\Models\EbookCollection;
use FilesystemIterator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\DomCrawler\Crawler;
use ZipArchive;

class BundleCollectionImporter
{
    private const ARCHIVE_EXTENSIONS = ['zip', 'rar', '7z', 'pdf', 'epub'];
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const GOOGLE_DRIVE_HOSTS = ['drive.google.com', 'docs.google.com'];

    public function importFromSource(string $source, array $options = []): array
    {
        $source = trim($source);

        if ($this->isGoogleDriveFolderUrl($source)) {
            return $this->importFromGoogleDriveFolderUrl($source, $options);
        }

        [$resolvedPath, $checkedPaths] = $this->resolveSourceDirectory($source);
        if (! $resolvedPath) {
            throw new RuntimeException('Source directory not found. Checked: ' . implode(', ', $checkedPaths));
        }

        return $this->importFromDirectory($resolvedPath, $options);
    }

    public function detectDuplicateSourceImport(string $source): array
    {
        $source = trim($source);

        if (! $this->isGoogleDriveFolderUrl($source)) {
            return [
                'is_duplicate' => false,
                'has_overlap' => false,
                'duplicate_count' => 0,
                'total_count' => 0,
                'matching_names' => [],
            ];
        }

        $deliverables = $this->expectedGoogleDriveDeliverables($source);
        if (empty($deliverables)) {
            return [
                'is_duplicate' => false,
                'has_overlap' => false,
                'duplicate_count' => 0,
                'total_count' => 0,
                'matching_names' => [],
            ];
        }

        $matches = EbookCollection::query()
            ->whereIn('download_url', array_keys($deliverables))
            ->orderBy('name')
            ->get(['name', 'download_url']);

        $duplicateCount = $matches->count();
        $totalCount = count($deliverables);

        return [
            'is_duplicate' => $duplicateCount > 0 && $duplicateCount === $totalCount,
            'has_overlap' => $duplicateCount > 0,
            'duplicate_count' => $duplicateCount,
            'total_count' => $totalCount,
            'matching_names' => $matches->pluck('name')->take(5)->all(),
        ];
    }

    public function resolveSourceDirectory(string $input): array
    {
        $input = trim($input);
        if ($input === '') {
            return [null, []];
        }

        $candidates = [];

        if ($this->isAbsolutePath($input)) {
            $candidates[] = $input;
        }

        $candidates[] = base_path($input);
        $candidates[] = storage_path($input);
        $candidates[] = public_path($input);

        $checked = [];

        foreach (array_values(array_unique($candidates)) as $candidate) {
            $candidate = $this->normalisePath($candidate);
            $checked[] = $candidate;

            if (is_dir($candidate)) {
                return [$candidate, $checked];
            }
        }

        return [null, $checked];
    }

    public function importFromDirectory(string $sourceDirectory, array $options = []): array
    {
        $sourceDirectory = $this->normalisePath($sourceDirectory);

        if (! is_dir($sourceDirectory)) {
            throw new RuntimeException("Directory [{$sourceDirectory}] was not found.");
        }

        $entries = $this->sourceEntries($sourceDirectory);
        if (empty($entries)) {
            throw new RuntimeException('No supported ZIP files or child folders were found in the source directory.');
        }

        $nextSortOrder = max(
            (int) EbookCollection::max('sort_order'),
            (int) ($options['sort_order_start'] ?? 0)
        );

        $result = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($entries as $entry) {
            try {
                $outcome = $this->importEntry($entry, $options, $nextSortOrder);

                if ($outcome === 'created') {
                    $result['created']++;
                } elseif ($outcome === 'updated') {
                    $result['updated']++;
                } else {
                    $result['skipped']++;
                }
            } catch (\Throwable $e) {
                $result['skipped']++;
                $result['errors'][] = basename($entry['path']) . ': ' . $e->getMessage();
            }
        }

        return $result;
    }

    private function importEntry(array $entry, array $options, int &$nextSortOrder): string
    {
        $path = $entry['path'];
        $isDirectory = $entry['type'] === 'directory';
        $baseName = $isDirectory
            ? basename($path)
            : pathinfo($path, PATHINFO_FILENAME);

        $displayName = $this->displayName($baseName);
        $baseSlug = Str::slug($baseName) ?: 'bundle-collection';

        $collection = EbookCollection::where('slug', $baseSlug)->first();
        $isNew = ! $collection;

        if (! $collection) {
            $collection = new EbookCollection();
            $collection->slug = $this->generateUniqueSlug($baseSlug);
            $collection->name = $displayName;
            $collection->excerpt = EbookCollection::fallbackExcerptFor($displayName);
            $collection->description = EbookCollection::fallbackDescriptionFor($displayName);
            $collection->sort_order = ++$nextSortOrder;
        }

        if (! $collection->name) {
            $collection->name = $displayName;
        }

        if (! $collection->excerpt) {
            $collection->excerpt = EbookCollection::fallbackExcerptFor($displayName);
        }

        if (! $collection->description) {
            $collection->description = EbookCollection::fallbackDescriptionFor($displayName);
        }

        if ($isNew || $collection->price === null) {
            $collection->price = $this->nullableDecimal($options['price'] ?? null);
        }

        if ($isNew || $collection->old_price === null) {
            $collection->old_price = $this->nullableDecimal($options['old_price'] ?? null);
        }

        if ($isNew || $collection->access_days === null) {
            $collection->access_days = $this->nullableInteger($options['access_days'] ?? null);
        }

        if ($isNew) {
            $collection->featured = (bool) ($options['featured'] ?? false);
            $collection->status = (bool) ($options['status'] ?? true);
        }

        $previousBundleFile = $collection->bundle_file;

        if ($isDirectory) {
            $collection->bundle_file = $this->archiveDirectoryToPublic($path, $collection->slug);
        } else {
            $collection->bundle_file = $this->copySourceFileToPublic(
                $path,
                'ebooks/collections/files',
                $collection->slug
            );
        }

        $collection->download_url = null;

        $coverCandidate = $isDirectory
            ? $this->findDirectoryCoverImage($path, $baseName)
            : $this->findSidecarCoverImage($path);

        if (! $collection->cover_image && $coverCandidate) {
            $collection->cover_image = $this->copySourceFileToPublic(
                $coverCandidate,
                'ebooks/collections/covers',
                $collection->slug . '-cover'
            );
        }

        $collection->save();

        if ($previousBundleFile && $previousBundleFile !== $collection->bundle_file) {
            $this->deletePublicFileIfExists($previousBundleFile);
        }

        return $isNew ? 'created' : 'updated';
    }

    private function sourceEntries(string $sourceDirectory): array
    {
        $entries = [];

        foreach (File::directories($sourceDirectory) as $directory) {
            $name = basename($directory);
            if (! Str::startsWith($name, '.')) {
                $entries[] = ['type' => 'directory', 'path' => $directory];
            }
        }

        foreach (File::files($sourceDirectory) as $file) {
            if (! $file instanceof SplFileInfo) {
                continue;
            }

            $name = $file->getFilename();
            if (Str::startsWith($name, '.')) {
                continue;
            }

            if (! $this->isSupportedArchive($name)) {
                continue;
            }

            $entries[] = ['type' => 'file', 'path' => $file->getPathname()];
        }

        usort($entries, function (array $left, array $right) {
            return strnatcasecmp(basename($left['path']), basename($right['path']));
        });

        return $entries;
    }

    private function isSupportedArchive(string $filename): bool
    {
        return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), self::ARCHIVE_EXTENSIONS, true);
    }

    private function archiveDirectoryToPublic(string $directory, string $slug): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP ZipArchive support is required to import folders as bundles.');
        }

        $files = File::allFiles($directory);
        if (empty($files)) {
            throw new RuntimeException('The folder is empty.');
        }

        $relativePath = 'ebooks/collections/files/' . Str::uuid() . '.zip';
        $absolutePath = public_path($relativePath);
        $destinationDirectory = dirname($absolutePath);

        if (! is_dir($destinationDirectory)) {
            mkdir($destinationDirectory, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create the bundle archive.');
        }

        $baseDirectory = rtrim($this->normalisePath($directory), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $itemPath = $item->getPathname();
            $relativeName = str_replace('\\', '/', Str::after($itemPath, $baseDirectory));

            if ($relativeName === '') {
                continue;
            }

            if ($item->isDir()) {
                $zip->addEmptyDir($relativeName);
                continue;
            }

            $zip->addFile($itemPath, $relativeName);
        }

        $zip->close();

        if (! is_file($absolutePath)) {
            throw new RuntimeException('The bundle archive was not created successfully.');
        }

        return $relativePath;
    }

    private function findDirectoryCoverImage(string $directory, string $baseName): ?string
    {
        $topLevelFiles = collect(File::files($directory))
            ->filter(fn ($file) => $file instanceof SplFileInfo)
            ->values();

        foreach (['cover', $baseName] as $candidateBase) {
            foreach (self::IMAGE_EXTENSIONS as $extension) {
                $candidate = $this->normalisePath($directory . DIRECTORY_SEPARATOR . $candidateBase . '.' . $extension);
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        $firstImage = $topLevelFiles->first(function (SplFileInfo $file) {
            return in_array(strtolower($file->getExtension()), self::IMAGE_EXTENSIONS, true);
        });

        return $firstImage?->getPathname();
    }

    private function findSidecarCoverImage(string $filePath): ?string
    {
        $directory = dirname($filePath);
        $baseName = pathinfo($filePath, PATHINFO_FILENAME);

        foreach ([$baseName, $baseName . '-cover', 'cover'] as $candidateBase) {
            foreach (self::IMAGE_EXTENSIONS as $extension) {
                $candidate = $this->normalisePath($directory . DIRECTORY_SEPARATOR . $candidateBase . '.' . $extension);
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    private function copySourceFileToPublic(string $sourcePath, string $directory, string $prefix): string
    {
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $relativePath = $directory . '/' . Str::slug($prefix) . '-' . Str::uuid() . ($extension ? '.' . $extension : '');
        $absolutePath = public_path($relativePath);
        $destinationDirectory = dirname($absolutePath);

        if (! is_dir($destinationDirectory)) {
            mkdir($destinationDirectory, 0755, true);
        }

        if (! @copy($sourcePath, $absolutePath)) {
            throw new RuntimeException('Unable to copy [' . basename($sourcePath) . '] into the public bundle directory.');
        }

        return $relativePath;
    }

    private function deletePublicFileIfExists(?string $relativePath): void
    {
        if (! $relativePath || filter_var($relativePath, FILTER_VALIDATE_URL)) {
            return;
        }

        $absolutePath = public_path($relativePath);
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (EbookCollection::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function displayName(string $value): string
    {
        return Str::of($value)
            ->replace(['_', '-'], ' ')
            ->squish()
            ->title()
            ->toString();
    }

    private function nullableDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1;
    }

    private function normalisePath(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public function isGoogleDriveFolderUrl(string $input): bool
    {
        if (! filter_var($input, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = strtolower((string) parse_url($input, PHP_URL_HOST));
        $path = (string) parse_url($input, PHP_URL_PATH);

        return in_array($host, self::GOOGLE_DRIVE_HOSTS, true)
            && preg_match('#/folders?/([a-zA-Z0-9_-]+)#', $path) === 1;
    }

    private function importFromGoogleDriveFolderUrl(string $folderUrl, array $options = []): array
    {
        $entries = $this->listGoogleDriveFolderEntries($folderUrl);

        $archives = [];
        $images = [];
        $folders = [];

        foreach ($entries as $entry) {
            if (($entry['type'] ?? null) === 'folder') {
                $folders[(string) $entry['id']] = $entry;
                continue;
            }

            $extension = strtolower(pathinfo((string) ($entry['name'] ?? ''), PATHINFO_EXTENSION));
            $baseKey = $this->normaliseBaseKey((string) ($entry['name'] ?? ''));

            if (in_array($extension, self::ARCHIVE_EXTENSIONS, true)) {
                $archives[$baseKey] = $entry;
                continue;
            }

            if (in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                $images[$baseKey] = $entry;
            }
        }

        if (empty($archives) && empty($folders)) {
            throw new RuntimeException('No supported ZIP, RAR, 7Z, PDF, EPUB files, or child folders were found in the Google Drive folder.');
        }

        $nextSortOrder = max(
            (int) EbookCollection::max('sort_order'),
            (int) ($options['sort_order_start'] ?? 0)
        );

        $result = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($archives as $baseKey => $archive) {
            try {
                $outcome = $this->importGoogleDriveArchive($archive, $images[$baseKey] ?? null, $options, $nextSortOrder);

                if ($outcome === 'created') {
                    $result['created']++;
                } elseif ($outcome === 'updated') {
                    $result['updated']++;
                } else {
                    $result['skipped']++;
                }
            } catch (\Throwable $e) {
                $result['skipped']++;
                $result['errors'][] = ($archive['name'] ?? 'bundle') . ': ' . $e->getMessage();
            }
        }

        foreach ($folders as $folder) {
            try {
                $outcome = $this->importGoogleDriveFolderBundle($folder, $options, $nextSortOrder);

                if ($outcome === 'created') {
                    $result['created']++;
                } elseif ($outcome === 'updated') {
                    $result['updated']++;
                } else {
                    $result['skipped']++;
                }
            } catch (\Throwable $e) {
                $result['skipped']++;
                $result['errors'][] = ($folder['name'] ?? 'folder bundle') . ': ' . $e->getMessage();
            }
        }

        return $result;
    }

    private function importGoogleDriveArchive(array $archive, ?array $cover, array $options, int &$nextSortOrder): string
    {
        $baseName = pathinfo((string) ($archive['name'] ?? ''), PATHINFO_FILENAME);
        $displayName = $this->displayName($baseName);
        $baseSlug = Str::slug($baseName) ?: 'bundle-collection';

        $collection = EbookCollection::where('slug', $baseSlug)->first();
        $isNew = ! $collection;

        if (! $collection) {
            $collection = new EbookCollection();
            $collection->slug = $this->generateUniqueSlug($baseSlug);
            $collection->name = $displayName;
            $collection->excerpt = EbookCollection::fallbackExcerptFor($displayName);
            $collection->description = EbookCollection::fallbackDescriptionFor($displayName);
            $collection->sort_order = ++$nextSortOrder;
        }

        if (! $collection->name) {
            $collection->name = $displayName;
        }

        if (! $collection->excerpt) {
            $collection->excerpt = EbookCollection::fallbackExcerptFor($displayName);
        }

        if (! $collection->description) {
            $collection->description = EbookCollection::fallbackDescriptionFor($displayName);
        }

        if ($isNew || $collection->price === null) {
            $collection->price = $this->nullableDecimal($options['price'] ?? null);
        }

        if ($isNew || $collection->old_price === null) {
            $collection->old_price = $this->nullableDecimal($options['old_price'] ?? null);
        }

        if ($isNew || $collection->access_days === null) {
            $collection->access_days = $this->nullableInteger($options['access_days'] ?? null);
        }

        if ($isNew) {
            $collection->featured = (bool) ($options['featured'] ?? false);
            $collection->status = (bool) ($options['status'] ?? true);
        }

        if ($collection->bundle_file) {
            $this->deletePublicFileIfExists($collection->bundle_file);
            $collection->bundle_file = null;
        }

        $collection->download_url = $this->googleDriveDownloadUrl((string) $archive['id']);

        if (! $collection->cover_image && ! empty($cover['id'])) {
            $collection->cover_image = $this->googleDriveImageUrl((string) $cover['id']);
        }

        $collection->save();

        return $isNew ? 'created' : 'updated';
    }

    private function importGoogleDriveFolderBundle(array $folder, array $options, int &$nextSortOrder): string
    {
        $folderName = trim((string) ($folder['name'] ?? ''));
        $displayName = $this->displayName($folderName !== '' ? $folderName : ((string) ($folder['id'] ?? 'bundle-folder')));
        $baseSlug = Str::slug($folderName !== '' ? $folderName : ((string) ($folder['id'] ?? 'bundle-folder'))) ?: 'bundle-folder';
        $folderUrl = $this->googleDriveFolderUrl(
            (string) ($folder['id'] ?? ''),
            is_string($folder['resource_key'] ?? null) ? $folder['resource_key'] : null
        );

        if ($folderUrl === null) {
            throw new RuntimeException('The Google Drive folder link could not be generated.');
        }

        $folderEntries = $this->listGoogleDriveFolderEntries($folderUrl);
        if (empty($folderEntries)) {
            throw new RuntimeException('The Google Drive child folder is empty or could not be read.');
        }

        $collection = EbookCollection::where('slug', $baseSlug)->first();
        $isNew = ! $collection;

        if (! $collection) {
            $collection = new EbookCollection();
            $collection->slug = $this->generateUniqueSlug($baseSlug);
            $collection->name = $displayName;
            $collection->excerpt = EbookCollection::fallbackExcerptFor($displayName);
            $collection->description = EbookCollection::fallbackDescriptionFor($displayName);
            $collection->sort_order = ++$nextSortOrder;
        }

        if (! $collection->name) {
            $collection->name = $displayName;
        }

        if (! $collection->excerpt) {
            $collection->excerpt = EbookCollection::fallbackExcerptFor($displayName);
        }

        if (! $collection->description) {
            $collection->description = EbookCollection::fallbackDescriptionFor($displayName);
        }

        if ($isNew || $collection->price === null) {
            $collection->price = $this->nullableDecimal($options['price'] ?? null);
        }

        if ($isNew || $collection->old_price === null) {
            $collection->old_price = $this->nullableDecimal($options['old_price'] ?? null);
        }

        if ($isNew || $collection->access_days === null) {
            $collection->access_days = $this->nullableInteger($options['access_days'] ?? null);
        }

        if ($isNew) {
            $collection->featured = (bool) ($options['featured'] ?? false);
            $collection->status = (bool) ($options['status'] ?? true);
        }

        if ($collection->bundle_file) {
            $this->deletePublicFileIfExists($collection->bundle_file);
            $collection->bundle_file = null;
        }

        $collection->download_url = $folderUrl;

        if (! $collection->cover_image) {
            $cover = collect($folderEntries)
                ->first(function (array $entry) use ($folderName) {
                    if (($entry['type'] ?? null) !== 'file') {
                        return false;
                    }

                    $extension = strtolower(pathinfo((string) ($entry['name'] ?? ''), PATHINFO_EXTENSION));
                    if (! in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                        return false;
                    }

                    $baseName = Str::lower(pathinfo((string) ($entry['name'] ?? ''), PATHINFO_FILENAME));
                    $folderBase = Str::lower($folderName);

                    return in_array($baseName, ['cover', Str::slug($folderBase), $folderBase], true);
                });

            if (! $cover) {
                $cover = collect($folderEntries)
                    ->first(function (array $entry) {
                        if (($entry['type'] ?? null) !== 'file') {
                            return false;
                        }

                        $extension = strtolower(pathinfo((string) ($entry['name'] ?? ''), PATHINFO_EXTENSION));

                        return in_array($extension, self::IMAGE_EXTENSIONS, true);
                    });
            }

            if (! empty($cover['id'])) {
                $collection->cover_image = $this->googleDriveImageUrl((string) $cover['id']);
            }
        }

        $collection->save();

        return $isNew ? 'created' : 'updated';
    }

    private function expectedGoogleDriveDeliverables(string $folderUrl): array
    {
        $entries = $this->listGoogleDriveFolderEntries($folderUrl);
        $deliverables = [];

        foreach ($entries as $entry) {
            if (($entry['type'] ?? null) === 'folder') {
                $folderUrl = $this->googleDriveFolderUrl(
                    (string) ($entry['id'] ?? ''),
                    is_string($entry['resource_key'] ?? null) ? $entry['resource_key'] : null
                );

                if ($folderUrl) {
                    $deliverables[$folderUrl] = (string) ($entry['name'] ?? 'Bundle Folder');
                }

                continue;
            }

            $extension = strtolower(pathinfo((string) ($entry['name'] ?? ''), PATHINFO_EXTENSION));
            if (! in_array($extension, self::ARCHIVE_EXTENSIONS, true)) {
                continue;
            }

            $fileId = (string) ($entry['id'] ?? '');
            if ($fileId === '') {
                continue;
            }

            $deliverables[$this->googleDriveDownloadUrl($fileId)] = (string) ($entry['name'] ?? 'Bundle File');
        }

        return $deliverables;
    }

    private function listGoogleDriveFolderEntries(string $folderUrl): array
    {
        $folderId = $this->extractGoogleDriveFolderId($folderUrl);
        if (! $folderId) {
            throw new RuntimeException('The Google Drive folder ID could not be detected from the provided URL.');
        }

        $resourceKey = $this->extractGoogleDriveResourceKey($folderUrl);
        $html = $this->fetchGoogleDriveFolderHtml($folderId, $resourceKey)
            ?: $this->fetchGoogleDriveFolderHtml($folderId, $resourceKey, false);

        if (! $html) {
            throw new RuntimeException('The Google Drive folder could not be read. Make sure the folder is public with "Anyone with the link can view".');
        }

        return array_values(array_filter(
            $this->parseGoogleDriveFolderHtml($html),
            fn (array $entry) => (string) ($entry['id'] ?? '') !== $folderId
        ));
    }

    private function fetchGoogleDriveFolderHtml(string $folderId, ?string $resourceKey = null, bool $embedded = true): ?string
    {
        $url = $embedded
            ? 'https://drive.google.com/embeddedfolderview?id=' . rawurlencode($folderId) . '#list'
            : 'https://drive.google.com/drive/folders/' . rawurlencode($folderId);

        if ($resourceKey) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . 'resourcekey=' . rawurlencode($resourceKey);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0 Safari/537.36',
            ])->withOptions([
                'verify' => false,
                'allow_redirects' => true,
            ])->timeout(30)->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $html = (string) $response->body();

        return trim($html) !== '' ? $html : null;
    }

    private function parseGoogleDriveFolderHtml(string $html): array
    {
        $crawler = new Crawler($html);
        $entries = [];

        foreach ($crawler->filter('a[href]') as $node) {
            $link = new Crawler($node);
            $name = trim(html_entity_decode($link->text('', false), ENT_QUOTES | ENT_HTML5));
            $href = trim((string) $link->attr('href'));

            if ($name === '' || $href === '') {
                continue;
            }

            $href = $this->normaliseGoogleDriveUrl($href);

            $folderId = $this->extractGoogleDriveFolderIdFromUrl($href);
            if ($folderId) {
                $entries['folder:' . $folderId] = [
                    'type' => 'folder',
                    'id' => $folderId,
                    'name' => $name,
                    'href' => $href,
                    'resource_key' => $this->extractGoogleDriveResourceKey($href),
                ];
                continue;
            }

            $fileId = $this->extractGoogleDriveFileIdFromUrl($href);
            if (! $fileId) {
                continue;
            }

            $entries['file:' . $fileId] = [
                'type' => 'file',
                'id' => $fileId,
                'name' => $name,
                'href' => $href,
            ];
        }

        if (empty($entries)) {
            throw new RuntimeException('No files or subfolders were found in the Google Drive folder HTML.');
        }

        return array_values($entries);
    }

    private function extractGoogleDriveFolderId(string $url): ?string
    {
        return $this->extractGoogleDriveFolderIdFromUrl($url);
    }

    private function extractGoogleDriveResourceKey(string $url): ?string
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return is_string($query['resourcekey'] ?? null) ? $query['resourcekey'] : null;
    }

    private function extractGoogleDriveFileIdFromUrl(string $url): ?string
    {
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5);

        if (str_starts_with($url, '/')) {
            $url = 'https://drive.google.com' . $url;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);

        if (preg_match('#/file/d/([a-zA-Z0-9_-]+)#', $path, $matches)) {
            return $matches[1];
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return is_string($query['id'] ?? null) ? $query['id'] : null;
    }

    private function extractGoogleDriveFolderIdFromUrl(string $url): ?string
    {
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5);
        $url = $this->normaliseGoogleDriveUrl($url);

        $path = (string) parse_url($url, PHP_URL_PATH);

        if (preg_match('#/folders?/([a-zA-Z0-9_-]+)#', $path, $matches)) {
            return $matches[1];
        }

        if (
            preg_match('#/(?:folderview|embeddedfolderview)$#', $path)
            || str_contains($path, '/drive/u/')
        ) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

            return is_string($query['id'] ?? null) ? $query['id'] : null;
        }

        return null;
    }

    private function normaliseGoogleDriveUrl(string $url): string
    {
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5);

        if (str_starts_with($url, '/')) {
            return 'https://drive.google.com' . $url;
        }

        return $url;
    }

    private function googleDriveFolderUrl(string $folderId, ?string $resourceKey = null): ?string
    {
        $folderId = trim($folderId);
        if ($folderId === '') {
            return null;
        }

        $url = 'https://drive.google.com/drive/folders/' . rawurlencode($folderId);

        if ($resourceKey) {
            $url .= '?resourcekey=' . rawurlencode($resourceKey);
        }

        return $url;
    }

    private function googleDriveDownloadUrl(string $fileId): string
    {
        return 'https://drive.google.com/uc?export=download&id=' . rawurlencode($fileId);
    }

    private function googleDriveImageUrl(string $fileId): string
    {
        return 'https://drive.google.com/thumbnail?id=' . rawurlencode($fileId) . '&sz=w1200';
    }

    private function normaliseBaseKey(string $filename): string
    {
        return Str::slug(pathinfo($filename, PATHINFO_FILENAME));
    }
}

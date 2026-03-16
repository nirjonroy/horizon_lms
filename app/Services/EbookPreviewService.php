<?php

namespace App\Services;

use App\Models\Ebook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Throwable;

class EbookPreviewService
{
    public const PAGE_LIMIT = 5;

    public function canPreview(Ebook $ebook): bool
    {
        return $this->resolveLocalPdfSource($ebook) !== null
            || ($this->remotePdfSource($ebook) !== null);
    }

    public function previewPageLimit(): int
    {
        return self::PAGE_LIMIT;
    }

    public function ensurePreview(Ebook $ebook): ?string
    {
        $previewPath = $this->previewPath($ebook);
        if (is_file($previewPath)) {
            return $previewPath;
        }

        $sourcePath = $this->resolveSourcePdfPath($ebook);
        if (! $sourcePath || ! is_file($sourcePath)) {
            return null;
        }

        $directory = dirname($previewPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($sourcePath);
            $pageLimit = min($pageCount, self::PAGE_LIMIT);

            for ($page = 1; $page <= $pageLimit; $page++) {
                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }

            $pdf->Output('F', $previewPath);
        } catch (Throwable $e) {
            Log::warning('Failed to generate ebook preview.', [
                'ebook_id' => $ebook->getKey(),
                'source' => $sourcePath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        return is_file($previewPath) ? $previewPath : null;
    }

    private function resolveSourcePdfPath(Ebook $ebook): ?string
    {
        $localPdf = $this->resolveLocalPdfSource($ebook);
        if ($localPdf) {
            return $localPdf;
        }

        return $this->cacheRemotePdfSource($ebook);
    }

    private function resolveLocalPdfSource(Ebook $ebook): ?string
    {
        if (! $ebook->ebook_file) {
            return null;
        }

        $extension = strtolower(pathinfo($ebook->ebook_file, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            return null;
        }

        $fullPath = public_path($ebook->ebook_file);

        return is_file($fullPath) ? $fullPath : null;
    }

    private function remotePdfSource(Ebook $ebook): ?string
    {
        $downloadUrl = $this->normaliseRemotePdfUrl($ebook->download_url, (string) ($ebook->format ?? ''));
        if ($downloadUrl) {
            return $downloadUrl;
        }

        return $this->normaliseRemotePdfUrl($ebook->source_url);
    }

    private function normaliseRemotePdfUrl(mixed $candidate, string $formatHint = ''): ?string
    {
        if (! is_string($candidate) || ! filter_var($candidate, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($candidate, PHP_URL_HOST));
        $path = strtolower((string) parse_url($candidate, PHP_URL_PATH));

        if (str_ends_with($path, '.pdf')) {
            return $candidate;
        }

        if (in_array($host, ['drive.google.com', 'docs.google.com'], true)) {
            $fileId = $this->extractGoogleDriveFileId($candidate);

            if ($fileId) {
                return 'https://drive.google.com/uc?export=download&id=' . $fileId;
            }
        }

        return str_contains(strtolower($formatHint), 'pdf') ? $candidate : null;
    }

    private function extractGoogleDriveFileId(string $url): ?string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);

        if (preg_match('#/file/d/([a-zA-Z0-9_-]+)#', $path, $matches)) {
            return $matches[1];
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return is_string($query['id'] ?? null) ? $query['id'] : null;
    }

    private function cacheRemotePdfSource(Ebook $ebook): ?string
    {
        $sourceUrl = $this->remotePdfSource($ebook);
        if (! $sourceUrl) {
            return null;
        }

        $path = $this->remoteSourcePath($ebook, $sourceUrl);
        if (is_file($path)) {
            return $path;
        }

        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        try {
            $response = Http::withOptions([
                'verify' => false,
                'allow_redirects' => true,
            ])->timeout(60)->accept('application/pdf')->get($sourceUrl);

            $body = $response->body();

            if (! $response->successful() || ! str_starts_with($body, '%PDF-')) {
                return null;
            }

            file_put_contents($path, $body);
        } catch (Throwable $e) {
            Log::warning('Failed to cache remote ebook PDF.', [
                'ebook_id' => $ebook->getKey(),
                'url' => $sourceUrl,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        return is_file($path) ? $path : null;
    }

    private function previewPath(Ebook $ebook): string
    {
        return storage_path('app/ebook-previews/' . sha1($ebook->getKey() . '|' . $this->previewFingerprint($ebook)) . '.pdf');
    }

    private function remoteSourcePath(Ebook $ebook, string $sourceUrl): string
    {
        return storage_path('app/ebook-preview-sources/' . sha1($ebook->getKey() . '|' . $sourceUrl) . '.pdf');
    }

    private function previewFingerprint(Ebook $ebook): string
    {
        return implode('|', [
            $ebook->updated_at?->timestamp ?? 0,
            $ebook->ebook_file ?? '',
            $ebook->download_url ?? '',
            $ebook->source_url ?? '',
        ]);
    }
}

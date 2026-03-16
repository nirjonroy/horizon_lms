<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EbookAccessPlan;
use App\Models\EbookCollection;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Services\EbookPreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $categories = $this->categoryMenu();

        $ebooks = Ebook::with('category')
            ->where('status', 1)
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('author', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.ebooks.index', [
            'ebooks' => $ebooks,
            'categories' => $categories,
            'activeCategory' => null,
            'search' => $search,
        ]);
    }

    public function category(Request $request, EbookCategory $category)
    {
        abort_unless($category->status, 404);

        $search = trim((string) $request->query('search', ''));
        $categories = $this->categoryMenu();

        $ebooks = Ebook::with('category')
            ->where('status', 1)
            ->where('category_id', $category->id)
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('author', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.ebooks.index', [
            'ebooks' => $ebooks,
            'categories' => $categories,
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    public function show(string $slug, EbookPreviewService $previewService)
    {
        $ebook = Ebook::with('category')
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedEbooks = Ebook::with('category')
            ->where('status', 1)
            ->where('id', '!=', $ebook->id)
            ->when($ebook->category_id, fn ($query) => $query->where('category_id', $ebook->category_id))
            ->latest('published_at')
            ->latest('id')
            ->take(4)
            ->get();

        $collectionIds = $ebook->collections()
            ->where('ebook_collections.status', 1)
            ->pluck('ebook_collections.id');

        $relatedCollections = EbookCollection::withCount(['ebooks' => function ($query) {
            $query->where('status', 1);
        }])
            ->where('status', 1)
            ->whereIn('id', $collectionIds)
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $relatedPlans = EbookAccessPlan::with('collection')
            ->where('status', 1)
            ->where(function ($query) use ($collectionIds) {
                $query->where('access_scope', EbookAccessPlan::SCOPE_ALL_EBOOKS)
                    ->orWhere(function ($inner) use ($collectionIds) {
                        $inner->where('access_scope', EbookAccessPlan::SCOPE_COLLECTION)
                            ->whereIn('ebook_collection_id', $collectionIds);
                    });
            })
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $hasPaidAccess = auth()->check() && $ebook->hasPaidAccess(auth()->id());
        $previewUrl = $previewService->canPreview($ebook)
            ? route('ebooks.preview', $ebook->slug)
            : null;
        $previewPageLimit = $previewService->previewPageLimit();

        return view('frontend.ebooks.show', compact(
            'ebook',
            'relatedEbooks',
            'relatedCollections',
            'relatedPlans',
            'hasPaidAccess',
            'previewUrl',
            'previewPageLimit'
        ));
    }

    public function download(string $slug)
    {
        $ebook = Ebook::query()
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to download this e-book.');
        }

        if (! $ebook->hasPaidAccess(auth()->id())) {
            return redirect()
                ->route('ebooks.show', $ebook->slug)
                ->with('error', 'Purchase this e-book to unlock the download.');
        }

        $localFile = $ebook->ebook_file ? public_path($ebook->ebook_file) : null;
        if ($localFile && is_file($localFile)) {
            $extension = pathinfo($localFile, PATHINFO_EXTENSION);
            $filename = Str::slug($ebook->title) . ($extension ? '.' . $extension : '');

            return response()->download($localFile, $filename);
        }

        $downloadLink = $ebook->downloadLink();
        if ($downloadLink) {
            return redirect()->away($downloadLink);
        }

        return redirect()
            ->route('ebooks.show', $ebook->slug)
            ->with('error', 'This e-book does not have a downloadable file yet.');
    }

    public function asset(Ebook $ebook, string $field)
    {
        abort_unless(in_array($field, [Ebook::IMAGE_FIELD_COVER, Ebook::IMAGE_FIELD_META], true), 404);

        $sourceUrl = $ebook->remoteImageSource($field);
        $relativePath = $ebook->cachedRemoteImageRelativePath($field);

        if (! $sourceUrl || ! $relativePath) {
            return redirect(asset('frontend/assets/images/img-loading.png'));
        }

        $fullPath = public_path($relativePath);

        if (! is_file($fullPath)) {
            $directory = dirname($fullPath);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            try {
                $response = Http::withOptions([
                    'verify' => false,
                    'allow_redirects' => true,
                ])->timeout(20)->accept('image/*')->get($sourceUrl);

                if (
                    $response->successful()
                    && Str::startsWith((string) $response->header('Content-Type'), 'image/')
                ) {
                    file_put_contents($fullPath, $response->body());
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to cache remote ebook image.', [
                    'ebook_id' => $ebook->getKey(),
                    'field' => $field,
                    'url' => $sourceUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! is_file($fullPath)) {
            return redirect(asset('frontend/assets/images/img-loading.png'));
        }

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    public function preview(string $slug, EbookPreviewService $previewService)
    {
        $ebook = Ebook::query()
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        $previewPath = $previewService->ensurePreview($ebook);
        if (! $previewPath || ! is_file($previewPath)) {
            return response(
                '<!doctype html><html><body style="font-family:Arial,sans-serif;padding:24px;color:#334155;">'
                . '<h3 style="margin-top:0;">Preview unavailable</h3>'
                . '<p>The preview could not be generated for this e-book right now.</p>'
                . '</body></html>',
                404,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return response()->file($previewPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . Str::slug($ebook->title ?: 'ebook') . '-preview.pdf"',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function categoryMenu()
    {
        return EbookCategory::withCount(['ebooks' => function ($query) {
            $query->where('status', 1);
        }])
            ->where('status', 1)
            ->whereHas('ebooks', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('name')
            ->get();
    }
}

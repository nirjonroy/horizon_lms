<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EbookCollection;
use Illuminate\Support\Str;

class EbookCollectionController extends Controller
{
    public function index()
    {
        $collections = EbookCollection::withCount(['ebooks' => function ($query) {
            $query->where('status', 1);
        }])
            ->publicCatalog()
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        return view('frontend.ebook_collections.index', compact('collections'));
    }

    public function show(string $slug)
    {
        $collection = EbookCollection::with(['ebooks' => function ($query) {
            $query->with('category')->orderByDesc('status')->latest('published_at')->latest('id');
        }, 'accessPlans' => function ($query) {
            $query->where('status', 1)->orderByDesc('featured')->orderBy('sort_order');
        }])
            ->publicCatalog()
            ->where('slug', $slug)
            ->firstOrFail();

        $hasAccess = auth()->check() && $collection->userHasAccess(auth()->id());

        return view('frontend.ebook_collections.show', compact('collection', 'hasAccess'));
    }

    public function download(string $slug)
    {
        $collection = EbookCollection::query()
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Login to download this bundle.');
        }

        if (! $collection->userHasAccess(auth()->id())) {
            return redirect()
                ->route('ebook-collections.show', $collection->slug)
                ->with('error', 'Purchase this bundle to unlock the download.');
        }

        $localFile = $collection->bundle_file ? public_path($collection->bundle_file) : null;
        if ($localFile && is_file($localFile)) {
            $extension = pathinfo($localFile, PATHINFO_EXTENSION);
            $filename = Str::slug($collection->name ?: 'bundle-collection') . ($extension ? '.' . $extension : '');

            return response()->download($localFile, $filename);
        }

        $downloadLink = $collection->downloadLink();
        if ($downloadLink) {
            return redirect()->away($downloadLink);
        }

        return redirect()
            ->route('ebook-collections.show', $collection->slug)
            ->with('error', 'This bundle does not have a downloadable file yet.');
    }
}

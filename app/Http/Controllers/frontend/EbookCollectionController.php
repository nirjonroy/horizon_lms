<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EbookCollection;

class EbookCollectionController extends Controller
{
    public function index()
    {
        $collections = EbookCollection::withCount(['ebooks' => function ($query) {
            $query->where('status', 1);
        }])
            ->where('status', 1)
            ->whereHas('ebooks', function ($query) {
                $query->where('status', 1);
            })
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        return view('frontend.ebook_collections.index', compact('collections'));
    }

    public function show(string $slug)
    {
        $collection = EbookCollection::with(['ebooks' => function ($query) {
            $query->where('status', 1)->with('category')->latest('published_at')->latest('id');
        }, 'accessPlans' => function ($query) {
            $query->where('status', 1)->orderByDesc('featured')->orderBy('sort_order');
        }])
            ->where('status', 1)
            ->where('slug', $slug)
            ->firstOrFail();

        $hasAccess = auth()->check() && $collection->userHasAccess(auth()->id());

        return view('frontend.ebook_collections.show', compact('collection', 'hasAccess'));
    }
}

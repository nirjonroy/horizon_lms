<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EbookAccessPlan;
use App\Models\EbookCollection;

class EbookAccessPlanController extends Controller
{
    public function index()
    {
        $plans = EbookAccessPlan::with('collection')
            ->publicCatalog()
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        $featuredCollections = EbookCollection::withCount(['ebooks' => function ($query) {
            $query->where('status', 1);
        }])
            ->where('status', 1)
            ->whereHas('ebooks', function ($query) {
                $query->where('status', 1);
            })
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('frontend.ebook_plans.index', compact('plans', 'featuredCollections'));
    }

    public function show(string $slug)
    {
        $plan = EbookAccessPlan::with([
            'collection',
            'collection.ebooks' => function ($query) {
                $query->where('status', 1)->with('category')->latest('published_at')->latest('id');
            },
        ])
            ->publicCatalog()
            ->where('slug', $slug)
            ->firstOrFail();

        $hasAccess = auth()->check() && $plan->userHasAccess(auth()->id());

        return view('frontend.ebook_plans.show', compact('plan', 'hasAccess'));
    }
}

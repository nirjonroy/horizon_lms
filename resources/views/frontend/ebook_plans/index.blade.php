@extends('frontend.app')

@section('title', 'E-Book Access Plans')

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">E-Book Access Plans</h1>
                <p class="section__desc text-white-50 mb-0">Choose monthly, yearly, lifetime, or bundle-based access to the Horizons e-book library.</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li>E-Book Plans</li>
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            @forelse($plans as $plan)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-item h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="badge badge-info mb-2 align-self-start">{{ $plan->durationLabel() }}</span>
                            <h3 class="card-title fs-22">
                                <a href="{{ route('ebook-plans.show', $plan->slug) }}">{{ $plan->name }}</a>
                            </h3>
                            @if($plan->tagline)
                                <p class="text-primary font-weight-bold mb-2">{{ $plan->tagline }}</p>
                            @endif
                            <p class="text-muted mb-3">{{ $plan->short_description ?: $plan->scopeLabel() }}</p>
                            <ul class="generic-list-item generic-list-item-flush text-muted mb-4">
                                <li><strong>Access:</strong> {{ $plan->scopeLabel() }}</li>
                                <li><strong>Billing:</strong> {{ ucfirst($plan->billing_cycle) }}</li>
                                <li><strong>Duration:</strong> {{ $plan->durationLabel() }}</li>
                            </ul>
                            <div class="mt-auto">
                                <div class="mb-3">
                                    <strong class="fs-24">${{ number_format((float) ($plan->price ?? 0), 2) }}</strong>
                                    @if($plan->old_price && (float) $plan->old_price > (float) $plan->price)
                                        <span class="before-price ms-2">${{ number_format((float) $plan->old_price, 2) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('ebook-plans.show', $plan->slug) }}" class="btn theme-btn w-100">View Plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No access plans are available right now.</div>
                </div>
            @endforelse
        </div>

        <div class="pt-3 d-flex justify-content-center">
            {{ $plans->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>

@if($featuredCollections->isNotEmpty())
    <section class="course-area pb-5">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fs-30 mb-1">Featured Bundle Collections</h2>
                    <p class="text-muted mb-0">Prefer curated packs? Start with a themed library.</p>
                </div>
                <a href="{{ route('ebook-collections.index') }}" class="btn btn-outline-secondary">Browse Collections</a>
            </div>
            <div class="row">
                @foreach($featuredCollections as $collection)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card card-item h-100">
                            <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title fs-20">
                                    <a href="{{ route('ebook-collections.show', $collection->slug) }}">{{ $collection->name }}</a>
                                </h3>
                                <p class="text-muted">{{ $collection->ebooks_count }} books included</p>
                                <a href="{{ route('ebook-collections.show', $collection->slug) }}" class="btn btn-sm theme-btn mt-auto">View Bundle</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection

@extends('frontend.app')

@php
    $seoSettings = \App\Models\SeoSetting::forPage('ebook-collections');
    $siteInfo = DB::table('site_information')->first();
    $replacements = ['collection_name' => $collection->name];
    $normalizeUrl = function ($path) {
        if (! $path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };

    $defaultTitleTemplate = \App\Models\SeoSetting::defaultTemplate('ebook-collections', 'seo_title')
        ?? '{collection_name} – Best eBook Bundle Online';
    $defaultDescriptionTemplate = \App\Models\SeoSetting::defaultTemplate('ebook-collections', 'seo_description')
        ?? 'Buy {collection_name} PDF eBooks with instant download. High-quality digital books at affordable prices. Start learning today.';
    $seoTitle = \App\Models\SeoSetting::applyTemplate(optional($seoSettings)->seo_title ?? $defaultTitleTemplate, $replacements);
    $seoDescription = \App\Models\SeoSetting::applyTemplate(optional($seoSettings)->seo_description ?? $defaultDescriptionTemplate, $replacements);
    $keywordsArray = \App\Models\SeoSetting::decodeKeywords(optional($seoSettings)->keywords, $replacements);
    $keywordsContent = ! empty($keywordsArray)
        ? implode(', ', $keywordsArray)
        : implode(', ', [$collection->name, 'ebook bundle', 'pdf ebooks', 'digital books']);
    $rawMetaImage = optional($seoSettings)->image ?: $collection->cover_image ?: ($siteInfo->logo ?? null);
    $metaImage = $normalizeUrl($rawMetaImage ?: $collection->coverImageUrl());
    $siteName = optional($seoSettings)->site_name ?? ($siteInfo->title ?? config('app.name'));
    $author = optional($seoSettings)->author ?? ($siteInfo->title ?? config('app.name'));
    $publisher = optional($seoSettings)->publisher ?? $author;
    $copyright = optional($seoSettings)->copyright ?? ($siteInfo->title ?? config('app.name'));
    $favicon = $normalizeUrl($siteInfo->logo ?? null);
@endphp

@section('title', $seoTitle)
@section('seos')
    <meta name="robots" content="index, follow">
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $keywordsContent }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta name="author" content="{{ $author }}">
    <meta name="publisher" content="{{ $publisher }}">
    <meta name="copyright" content="{{ $copyright }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @if($favicon)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
@endsection

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">{{ $collection->name }}</h1>
                <p class="section__desc text-white-50 mb-0">{{ $collection->summaryText() }}</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><a href="{{ route('ebook-collections.index') }}">Bundle Collections</a></li>
                <li>{{ $collection->name }}</li>
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card card-item">
                    <div class="card-body text-center">
                        <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" class="img-fluid rounded mb-4" style="max-height: 260px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                        <div class="mb-3">
                            <strong class="fs-30">${{ number_format((float) ($collection->price ?? 0), 2) }}</strong>
                            @if($collection->old_price && (float) $collection->old_price > (float) $collection->price)
                                <div class="before-price fs-18 mt-1">${{ number_format((float) $collection->old_price, 2) }}</div>
                            @endif
                        </div>
                        <p class="text-muted">{{ $collection->accessLabel() }}</p>
                        <div class="d-grid gap-2">
                            @if($hasAccess)
                                @if($collection->hasDeliverable())
                                    <a href="{{ route('ebook-collections.download', $collection->slug) }}" class="btn theme-btn w-100">{{ $collection->deliverableActionLabel() }}</a>
                                @endif
                                @if($collection->ebooks->isNotEmpty())
                                    <a href="{{ route('ebooks.index') }}" class="btn btn-outline-secondary w-100">Browse Included E-Books</a>
                                @endif
                                <p class="small text-success mb-0">This bundle is already unlocked on your account.</p>
                            @elseif($collection->canBePurchased())
                                @auth
                                    <form action="{{ route('ebook-collections.cart.add', $collection->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn theme-btn w-100">Add Bundle to Cart</button>
                                    </form>
                                    <a href="{{ route('ebook-collections.cart.buy_now', $collection->id) }}" class="btn btn-dark w-100">Buy Now</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn theme-btn w-100">Login to Purchase</a>
                                @endauth
                            @else
                                <div class="alert alert-light border text-start mb-0">This bundle is not currently available.</div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($collection->accessPlans->isNotEmpty())
                    <div class="card card-item mt-4">
                        <div class="card-body">
                            <h3 class="widget-title border-bottom pb-3 mb-4">Available Through Plans</h3>
                            <ul class="generic-list-item">
                                @foreach($collection->accessPlans as $plan)
                                    <li>
                                        <a href="{{ route('ebook-plans.show', $plan->slug) }}">{{ $plan->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="card card-item mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">About This Bundle</h2>
                        <h3 class="fs-24 text-primary mb-3">{{ $collection->name }}</h3>
                        @if($collection->hasMeaningfulDescription())
                            <div class="description-content text-muted mb-0">{!! $collection->description !!}</div>
                        @else
                            <p class="text-muted mb-0">{{ $collection->aboutText() }}</p>
                        @endif
                    </div>
                </div>

                @if($collection->ebooks->isNotEmpty())
                    <div class="card card-item">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                                <div>
                                    <h3 class="widget-title mb-1">Books in This Bundle</h3>
                                    <p class="text-muted mb-0">{{ $collection->ebooks->count() }} books in this bundle</p>
                                </div>
                            </div>
                            <div class="row">
                                @foreach($collection->ebooks as $ebook)
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border">
                                            <img src="{{ $ebook->coverImageUrl() }}" alt="{{ $ebook->title }}" class="card-img-top" style="height: 220px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                                            <div class="card-body d-flex flex-column">
                                                <span class="badge badge-light mb-2 align-self-start">
                                                    {{ $ebook->status ? 'Available Now' : 'Included In Bundle' }}
                                                </span>
                                                <h4 class="fs-18">
                                                    @if($ebook->status)
                                                        <a href="{{ route('ebooks.show', $ebook->slug) }}">{{ $ebook->title }}</a>
                                                    @else
                                                        {{ $ebook->title }}
                                                    @endif
                                                </h4>
                                                <p class="text-muted mb-3">{{ $ebook->author ?? 'Unknown author' }}</p>
                                                @if($ebook->status)
                                                    <a href="{{ route('ebooks.show', $ebook->slug) }}" class="btn btn-sm theme-btn mt-auto">View Book</a>
                                                @else
                                                    <span class="btn btn-sm btn-outline-secondary mt-auto disabled" aria-disabled="true">Bundle Item</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($collection->hasDeliverable())
                    <div class="card card-item">
                        <div class="card-body">
                            <h3 class="widget-title mb-3">Bundle Download</h3>
                            <p class="text-muted mb-0">{{ $collection->deliverableDescription() }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

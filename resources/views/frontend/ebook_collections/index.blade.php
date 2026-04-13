@extends('frontend.app')

@php
    $seoSettings = \App\Models\SeoSetting::forPage('ebook-collections');
    $siteInfo = DB::table('site_information')->first();
    $replacements = ['collection_name' => 'BooksToGo Bundle Collections'];
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
        : 'BooksToGo Bundle Collections, ebook bundle, pdf ebooks, digital books';
    $firstCollection = method_exists($collections, 'items') ? collect($collections->items())->first() : null;
    $rawMetaImage = optional($seoSettings)->image ?: ($firstCollection?->cover_image) ?: ($siteInfo->logo ?? null);
    $metaImage = $normalizeUrl($rawMetaImage ?: ($firstCollection?->coverImageUrl()));
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
                <h1 class="section__title text-white">Bundle Collections</h1>
                <p class="section__desc text-white-50 mb-0">Buy curated e-book sets for a single price and unlock the full collection instantly after checkout.</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li>Bundle Collections</li>
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            @forelse($collections as $collection)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-item h-100">
                        <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" class="card-img-top" style="height: 260px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                        <div class="card-body d-flex flex-column">
                            <span class="badge badge-light mb-2 align-self-start">
                                {{ $collection->ebooks_count > 0 ? $collection->ebooks_count . ' books' : 'Direct bundle download' }}
                            </span>
                            <h3 class="card-title fs-22">
                                <a href="{{ route('ebook-collections.show', $collection->slug) }}">{{ $collection->name }}</a>
                            </h3>
                            <p class="text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($collection->summaryText(), 110) }}</p>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <strong>${{ number_format((float) ($collection->price ?? 0), 2) }}</strong>
                                <a href="{{ route('ebook-collections.show', $collection->slug) }}" class="btn btn-sm theme-btn">View Bundle</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No bundle collections are available right now.</div>
                </div>
            @endforelse
        </div>

        <div class="pt-3 d-flex justify-content-center">
            {{ $collections->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>
@endsection

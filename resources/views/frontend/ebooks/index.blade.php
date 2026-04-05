@extends('frontend.app')

@php
    $SeoSettings = \App\Models\SeoSetting::forPage('ebooks');
    $siteInfo = DB::table('site_information')->first();
    $keywordsArray = $SeoSettings && $SeoSettings->keywords ? json_decode($SeoSettings->keywords, true) : [];
    if (! is_array($keywordsArray)) {
        $keywordsArray = [];
    }

    $normalizeUrl = function ($path) {
        if (! $path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };

    $firstEbook = method_exists($ebooks, 'items') ? collect($ebooks->items())->first() : null;
    $firstEbookImage = $firstEbook ? $firstEbook->metaImageUrl() : null;
    $fallbackTitle = $activeCategory ? $activeCategory->name . ' E-Books' : 'E-Books';
    $fallbackDescription = $activeCategory && $activeCategory->description
        ? strip_tags($activeCategory->description)
        : 'Browse uploaded and imported e-books by category.';
    $rawMetaImage = $activeCategory?->image ?: optional($SeoSettings)->image ?: $firstEbookImage ?: ($siteInfo->logo ?? null);
    $metaImage = $normalizeUrl($rawMetaImage);
    $seoTitle = $activeCategory ? $fallbackTitle : (optional($SeoSettings)->seo_title ?? $fallbackTitle);
    $seoDescription = $activeCategory ? $fallbackDescription : (optional($SeoSettings)->seo_description ?? $fallbackDescription);
    $siteName = optional($SeoSettings)->site_name ?? ($siteInfo->title ?? config('app.name'));
    $seoAuthor = optional($SeoSettings)->author ?? ($siteInfo->title ?? config('app.name'));
    $publisher = optional($SeoSettings)->publisher ?? $seoAuthor;
    $copyright = optional($SeoSettings)->copyright ?? ($siteInfo->title ?? config('app.name'));
    $favicon = $normalizeUrl($siteInfo->logo ?? null);
    $keywordsContent = ! empty($keywordsArray) ? implode(', ', $keywordsArray) : ($activeCategory ? $activeCategory->name . ', ebooks' : 'ebooks');
    $robots = 'index, follow';
@endphp

@section('title', $seoTitle)
@section('seos')
    <meta name="robots" content="{{ $robots }}">
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($seoDescription, 160, '') }}">
    <meta name="keywords" content="{{ $keywordsContent }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($seoDescription, 160, '') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta name="author" content="{{ $seoAuthor }}">
    <meta name="publisher" content="{{ $publisher }}">
    <meta name="copyright" content="{{ $copyright }}">
    <meta name="language" content="english">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    <link rel="canonical" href="{{ url()->current() }}">
    @if($favicon)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($seoDescription, 160, '') }}">
    @if($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:site" content="{{ url()->current() }}">
@endsection

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">{{ $seoTitle }}</h1>
                <p class="section__desc text-white-50 mb-0">{{ $seoDescription }}</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><a href="{{ route('ebooks.index') }}">E-Books</a></li>
                @if($activeCategory)
                    <li>{{ $activeCategory->name }}</li>
                @endif
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="sidebar">
                    <div class="card card-item mb-4">
                        <div class="card-body">
                            <h3 class="widget-title border-bottom pb-3 mb-4">Search E-Books</h3>
                            <form method="GET" action="{{ $activeCategory ? route('ebooks.category.show', $activeCategory) : route('ebooks.index') }}">
                                <div class="form-group mb-2">
                                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Search books or authors">
                                </div>
                                <button type="submit" class="btn theme-btn w-100">Search</button>
                            </form>
                        </div>
                    </div>

                    <div class="card card-item mb-4">
                        <div class="card-body">
                            <h3 class="widget-title border-bottom pb-3 mb-4">Categories</h3>
                            <ul class="generic-list-item">
                                <li>
                                    <a href="{{ route('ebooks.index') }}" class="{{ ! $activeCategory ? 'text-primary font-weight-bold' : '' }}">
                                        All E-Books
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('ebooks.category.show', $category) }}" class="{{ optional($activeCategory)->id === $category->id ? 'text-primary font-weight-bold' : '' }}">
                                            {{ $category->name }}
                                            <span class="badge badge-light ms-2">{{ $category->ebooks_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="card card-item">
                        <div class="card-body text-center">
                            <h3 class="widget-title mb-3">Looking for a course?</h3>
                            <p class="text-muted mb-3">Use the academy catalog for full learning paths and premium training.</p>
                            <a href="{{ route('premium-courses') }}" class="theme-btn w-100">Browse Courses</a>
                        </div>
                    </div>

                    <div class="card card-item mt-4">
                        <div class="card-body">
                            <h3 class="widget-title mb-3">Need Library Access?</h3>
                            <p class="text-muted mb-3">Unlock all books with an active access plan, or choose a curated bundle collection.</p>
                            <a href="{{ route('ebook-plans.index') }}" class="btn theme-btn w-100 mb-2">View Access Plans</a>
                            <a href="{{ route('ebook-collections.index') }}" class="btn btn-outline-secondary w-100">View Bundle Collections</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <p class="mb-2 mb-lg-0">
                        Showing <strong>{{ $ebooks->total() }}</strong> {{ \Illuminate\Support\Str::plural('book', $ebooks->total()) }}
                        @if($search !== '')
                            for "{{ $search }}"
                        @endif
                    </p>
                    @if($search !== '')
                        <a href="{{ $activeCategory ? route('ebooks.category.show', $activeCategory) : route('ebooks.index') }}" class="btn btn-sm btn-outline-secondary">
                            Clear Search
                        </a>
                    @endif
                </div>

                <div class="row">
                    @forelse($ebooks as $ebook)
                        @php
                            $cover = $ebook->coverImageUrl();
                        @endphp
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card card-item h-100">
                                <div class="card-image">
                                    <a href="{{ route('ebooks.show', $ebook->slug) }}">
                                        <img src="{{ $cover }}" class="card-img-top" alt="{{ $ebook->title }}" style="height: 260px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                                    </a>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <span class="ribbon ribbon-blue-bg fs-14 mb-2">{{ optional($ebook->category)->name ?? 'E-Book' }}</span>
                                    <h3 class="card-title fs-18">
                                        <a href="{{ route('ebooks.show', $ebook->slug) }}">{{ \Illuminate\Support\Str::limit($ebook->title, 60) }}</a>
                                    </h3>
                                    <p class="text-muted mb-2">{{ $ebook->author ?? 'Unknown author' }}</p>
                                    <p class="card-text text-muted small flex-grow-1">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($ebook->excerpt ?: $ebook->description ?: ''), 110) ?: 'View details to read more about this title.' }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between mt-auto pt-3">
                                        <div>
                                            @if($ebook->price !== null)
                                                <strong>${{ number_format((float) $ebook->price, 2) }}</strong>
                                                @if($ebook->old_price)
                                                    <span class="before-price">${{ number_format((float) $ebook->old_price, 2) }}</span>
                                                @endif
                                            @else
                                                <strong>{{ $ebook->canBePurchased() ? 'Purchase Required' : 'View Details' }}</strong>
                                            @endif
                                        </div>
                                        <a href="{{ route('ebooks.show', $ebook->slug) }}" class="btn btn-sm theme-btn">Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No e-books matched your current filters.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="pt-3">
                    {{ $ebooks->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


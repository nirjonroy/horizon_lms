@extends('frontend.app')
@php
    $SeoSettings = \App\Models\SeoSetting::forPage('horizons-global-academy-free-courses-deal');
    $siteInfo = DB::table('site_information')->first();
    $keywordsArray = $SeoSettings && $SeoSettings->keywords ? json_decode($SeoSettings->keywords, true) : [];
    if (!is_array($keywordsArray)) {
        $keywordsArray = [];
    }
    $normalizeUrl = function ($path) {
        if (!$path) {
            return null;
        }
        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };
    $firstCourse = isset($full_access) ? (is_iterable($full_access) ? collect($full_access)->first() : null) : null;
    $firstCourseImage = $firstCourse && isset($firstCourse->image) ? $firstCourse->image : null;
    $rawMetaImage = optional($SeoSettings)->image ?: $firstCourseImage ?: ($siteInfo->logo ?? null);
    $metaImage = $normalizeUrl($rawMetaImage);
    $seoTitle = optional($SeoSettings)->seo_title ?? config('app.name');
    $seoDescription = optional($SeoSettings)->seo_description ?? '';
    $siteName = optional($SeoSettings)->site_name ?? $seoTitle;
    $author = optional($SeoSettings)->author ?? ($siteInfo->title ?? config('app.name'));
    $publisher = optional($SeoSettings)->publisher ?? $author;
    $copyright = optional($SeoSettings)->copyright ?? ($siteInfo->title ?? config('app.name'));
    $favicon = $normalizeUrl($siteInfo->logo ?? null);
    $keywordsContent = !empty($keywordsArray) ? implode(', ', $keywordsArray) : '';
    $freeCourses = collect($full_access ?? []);
    $courseCount = $freeCourses->count();
    $heroStyles = 'background-color: #0d1938; padding: 80px 0; min-height: 260px;';
    $pageTitle = 'Free Learning Hub';
    $pageSummary = 'Claim self-paced micro courses, templates, and guided exercises curated by Horizons Global Academy.';
@endphp
@section('title', $seoTitle)
@section('seos')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $keywordsContent }}">

    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <!--<meta property="og:image:width" content="1200">-->
    <!--<meta property="og:image:height" content="628">-->

    <meta name="author" content="{{ $author }}">
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
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:site" content="{{ url()->current() }}">
@endsection
@section('content')
    <style>
        .free-courses-hero .overlay {
            background: rgba(13, 25, 56, 0.35);
        }
    </style>
    <section class="breadcrumb-area section-padding img-bg-2 free-courses-hero" style="{{ $heroStyles }}; padding: 30px">
        <!--<div class="overlay"></div>-->
        <div class="container">
            <div class="breadcrumb-content text-center">
                <p class="badge bg-white text-primary text-uppercase letter-spacing-1 mb-3">
                    100% Free Collection
                </p>
                <h1 class="section__title text-white mb-3">{{ $pageTitle }}</h1>
                <p class="section__desc text-white-50 mx-auto" style="max-width: 620px;">
                    {{ $pageSummary }}
                </p>
                <div class="d-flex justify-content-center gap-4 flex-wrap mt-4 text-white-50">
                    <span><i class="la la-play-circle me-1 text-white"></i>{{ $courseCount }} {{ \Illuminate\Support\Str::plural('course', $courseCount) }}</span>
                    <span><i class="la la-clock me-1 text-white"></i>Self-paced micro lessons</span>
                    <span><i class="la la-infinity me-1 text-white"></i>Lifetime access</span>
                </div>
            </div>
        </div>
    </section>

    <section class="course-area ">
        <div class="container">
            <div class="filter-bar mb-4">
                <div class="filter-bar-inner d-flex flex-wrap align-items-center justify-content-between">
                    <p class="fs-14 mb-3 mb-lg-0">
                        Unlock <span class="text-black">{{ $courseCount }}</span> curated {{ \Illuminate\Support\Str::plural('course', $courseCount) }}—all free forever.
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('premium-courses') }}" class="btn theme-btn theme-btn-sm">
                            Explore Premium Catalog
                        </a>
                        <a href="{{ route('contact.us') }}" class="btn theme-btn theme-btn-sm theme-btn-white">
                            Talk to an Advisor
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse($freeCourses as $course)
                    @php
                        $image = $normalizeUrl($course->image ?? null) ?: asset('frontend/assets/images/img-loading.png');
                        $courseUrl = route('course.show', $course->slug);
                        $duration = $course->duration ?: 'Self-paced';
                        $modules = $course->questions ? $course->questions . ' modules' : 'Guided lessons';
                        $priceLabel = 'Free';
                        $oldPrice = is_numeric($course->old_price ?? null) ? number_format((float) $course->old_price, 2) : null;
                        $excerpt = \Illuminate\Support\Str::limit(strip_tags($course->short_description), 130);
                    @endphp
                    <div class="col-lg-4 responsive-column-half">
                        <div class="card card-item card-preview mb-4 h-100">
                            <div class="card-image">
                                <a href="{{ $courseUrl }}" class="d-block">
                                    <img class="card-img-top lazy" src="{{ $image }}" data-src="{{ $image }}" alt="{{ $course->title }}">
                                </a>
                                <div class="course-badge-labels">
                                    <div class="course-badge blue">Free</div>
                                    <div class="course-badge">{{ $course->format ?? 'Online' }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $duration }}</h6>
                                <h5 class="card-title">
                                    <a href="{{ $courseUrl }}">{{ $course->title }}</a>
                                </h5>
                                <p class="card-text text-muted fs-14 mb-3">{{ $excerpt }}</p>
                                <div class="d-flex flex-wrap text-gray fs-13 mb-3">
                                    <span class="me-3"><i class="la la-user me-1"></i>{{ $course->instructor }}</span>
                                    <span><i class="la la-layer-group me-1"></i>{{ $modules }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="card-price text-success font-weight-bold mb-0">
                                        {{ $priceLabel }}
                                        @if($oldPrice)
                                            <span class="before-price font-weight-medium text-muted">
                                                {{ $oldPrice }}
                                            </span>
                                        @endif
                                    </p>
                                    <a href="{{ $courseUrl }}" class="btn theme-btn theme-btn-sm">
                                        Start Free
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <img src="{{ asset('frontend/assets/images/img-loading.png') }}" alt="Empty state" class="mb-4" style="max-width: 120px;">
                            <h4 class="mb-2">No free courses available yet</h4>
                            <p class="text-muted mb-3">Our team is crafting brand-new resources. Check back soon or browse the premium library.</p>
                            <a href="{{ route('premium-courses') }}" class="btn theme-btn">Browse Premium Courses</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

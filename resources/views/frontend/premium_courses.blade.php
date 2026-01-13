@extends('frontend.app')
@php
    $showBundlesOnly = $showBundlesOnly ?? false;
    $SeoSettings = \App\Models\SeoSetting::forPage('horizons-global-academy-courses-certificates-robust-deal');
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
    $firstCourseImage = null;
    if ($showBundlesOnly && isset($full_access)) {
        $fullAccessCollection = is_iterable($full_access) ? collect($full_access) : collect();
        $firstFullAccess = $fullAccessCollection->first();
        if ($firstFullAccess && isset($firstFullAccess->image)) {
            $firstCourseImage = $firstFullAccess->image;
        }
    }
    if (!$firstCourseImage && isset($all_courses)) {
        if (method_exists($all_courses, 'first')) {
            $firstCourse = $all_courses->first();
        } else {
            $firstCourse = is_iterable($all_courses) ? collect($all_courses)->first() : null;
        }
        if ($firstCourse && isset($firstCourse->image)) {
            $firstCourseImage = $firstCourse->image;
        }
    }
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
    @php
        $showBundlesOnly = $showBundlesOnly ?? false;
        $search = $search ?? '';
        $sort = $sort ?? 'newest';
        $activeCategory = $activeCategory ?? null;
        $activeSubcategory = $activeSubcategory ?? null;
        $activeChildCategory = $activeChildCategory ?? null;
        $priceFilter = $priceFilter ?? ['min' => null, 'max' => null];

        $bundleItems = collect((isset($full_access) && method_exists($full_access, 'items')) ? $full_access->items() : ($full_access ?? []));
        $bundlePaginator = (isset($full_access) && method_exists($full_access, 'total')) ? $full_access : null;
        $bundleTotal = $bundlePaginator ? $bundlePaginator->total() : $bundleItems->count();

        $totalAvailableCourses = $showBundlesOnly ? $bundleTotal : ($all_courses?->total() ?? 0);
        $priceFloor = isset($priceStats) && $priceStats->min_price !== null ? (float) $priceStats->min_price : 0;
        $priceCeil = isset($priceStats) && $priceStats->max_price !== null ? (float) $priceStats->max_price : 0;
        $selectedMin = $priceFilter['min'] ?? null;
        $selectedMax = $priceFilter['max'] ?? null;
        $hasActiveFilters = $search !== '' || $activeCategory || $activeSubcategory || $activeChildCategory || $selectedMin || $selectedMax;
        $buildQuery = function (array $overrides = []) {
            $query = array_merge(request()->query(), $overrides);
            return array_filter($query, function ($value) {
                if (is_array($value)) {
                    return !empty($value);
                }
                return !is_null($value) && $value !== '';
            });
        };
        $pageHeading = $showBundlesOnly
            ? 'Unlimited & Bundle Programs'
            : ($activeChildCategory->name ?? $activeSubcategory->name ?? $activeCategory->name ?? 'Courses & Certificates');
        $pageDescription = $showBundlesOnly
            ? 'Unlock curated learning paths, guided cohorts, and every premium resource.'
            : 'Build job-ready skills with ' . config('app.name') . '. Flexible, instructor-led, and designed for real outcomes.';
        $listingRoute = $showBundlesOnly ? route('bundle-programs') : route('premium-courses');
    @endphp

    <section class="breadcrumb-area section-padding img-bg-2" style="padding:50px">
        <div class="overlay"></div>
        <div class="container">
            <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between" >
                <div class="section-heading mb-3 mb-lg-0">
                    <h2 class="section__title text-white">{{ $pageHeading }}</h2>
                    <p class="section__desc text-white-50 mb-0">
                        {{ $pageDescription }}
                    </p>
                </div>
                <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li>{{ $showBundlesOnly ? 'Bundle Programs' : 'Courses' }}</li>
                    @if($activeCategory)
                        <li>{{ $activeCategory->name }}</li>
                    @endif
                    @if($activeSubcategory)
                        <li>{{ $activeSubcategory->name }}</li>
                    @endif
                    @if($activeChildCategory)
                        <li>{{ $activeChildCategory->name }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </section>

    <section class="course-area section--padding" style="padding-top:50px">
        <div class="container">
            <div class="filter-bar mb-4">
                <div class="filter-bar-inner d-flex flex-wrap align-items-center justify-content-between">
                    <p class="fs-14 mb-2 mb-md-0">
                        We found <span class="text-black">{{ $totalAvailableCourses }}</span> {{ $showBundlesOnly ? 'programs' : 'courses' }} available for you
                        @if($search !== '')
                            <span class="text-muted">matching “{{ $search }}”</span>
                        @endif
                    </p>
                    <div class="d-flex flex-wrap align-items-center">
                        <ul class="filter-nav me-3">
                            <li>
                                <a href="javascript:void(0)" data-toggle="tooltip" title="Grid View" class="active">
                                    <span class="la la-th-large"></span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" data-toggle="tooltip" title="List View">
                                    <span class="la la-list"></span>
                                </a>
                            </li>
                        </ul>
                        <form method="GET" action="{{ $listingRoute }}" class="d-flex align-items-center">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <input type="hidden" name="category" value="{{ optional($activeCategory)->slug }}">
                            <input type="hidden" name="subcategory" value="{{ optional($activeSubcategory)->slug }}">
                            <input type="hidden" name="child" value="{{ optional($activeChildCategory)->slug }}">
                            <input type="hidden" name="price_min" value="{{ $selectedMin }}">
                            <input type="hidden" name="price_max" value="{{ $selectedMax }}">
                            <div class="select-container select--container">
                                <select class="select-container-select" name="sort" onchange="this.form.submit()">
                                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest courses</option>
                                    <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest courses</option>
                                    <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price: high to low</option>
                                    <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: low to high</option>
                                    <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Alphabetical</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-5">
                    @if($showBundlesOnly && $bundleItems->isNotEmpty())
                        <div class="section-heading mb-3">
                            <h5 class="section__title fs-20 mb-1">Unlimited &amp; Bundle Programs</h5>
                            <p class="section__desc mb-0">Unlock curated learning paths, guided cohorts, and every premium resource.</p>
                        </div>
                        <div class="row">
                            @foreach($bundleItems as $bundle)
                                @php
                                    $placeholderImage = asset('frontend/assets/images/img-loading.png');
                                    $rawImage = $bundle->image;
                                    if ($rawImage && !\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                                        $rawImage = asset($rawImage);
                                    }
                                    $bundleImage = $rawImage ?: $placeholderImage;
                                    $bundlePricing = \App\Services\CampaignService::pricingForCourse($bundle);
                                    $bundlePrice = $bundlePricing->sale_price;
                                    $bundleOldPrice = $bundlePricing->strike_price;
                                    $hasBundleDiscount = $bundlePricing->has_discount || ($bundleOldPrice && $bundlePrice !== null && $bundleOldPrice > $bundlePrice);
                                    $bundleDiscount = ($hasBundleDiscount && $bundleOldPrice && $bundlePrice !== null && $bundleOldPrice > 0)
                                        ? max(1, round((($bundleOldPrice - $bundlePrice) / $bundleOldPrice) * 100))
                                        : null;
                                    $bundleType = ucfirst($bundle->type ?? 'Bundle');
                                    $bundleTooltipId = 'tooltip-bundle-' . $bundle->id;
                                    $bundleDescription = \Illuminate\Support\Str::limit(strip_tags($bundle->short_description ?? ''), 110);
                                    $bundleTooltipDescription = \Illuminate\Support\Str::limit(strip_tags($bundle->short_description ?: $bundle->long_description ?? ''), 220);
                                    $bundleUpdatedLabel = optional($bundle->updated_at)->format('F Y');
                                    $bundleDurationLabel = $bundle->duration ?: 'Self-paced';
                                    $bundleEffortLabel = $bundle->effort ?: 'Flexible schedule';
                                    $bundleFormatLabel = $bundle->format ?: 'Online learning';
                                    $bundleLevelLabel = $bundle->questions
                                        ? $bundle->questions . ' modules'
                                        : 'All learners welcome';
                                @endphp
                                <div class="col-lg-6 responsive-column-half">
                                    <div class="card card-item card-preview h-100" data-tooltip-content="#{{ $bundleTooltipId }}">
                                    <div class="card-image">
                                        <a href="{{ route('course.show', $bundle->slug) }}" class="d-block">
                                            <img class="card-img-top lazy" src="{{ $placeholderImage }}" data-src="{{ $bundleImage }}" alt="{{ $bundle->title }}">
                                        </a>
                                        <div class="course-badge-labels">
                                            <div class="course-badge">{{ $bundleType }}</div>
                                            @if($bundleDiscount)
                                                <div class="course-badge blue">-{{ $bundleDiscount }}%</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <span class="ribbon ribbon-blue-bg fs-14 mb-3">{{ __('Full access bundle') }}</span>
                                        <h2 class="card-title">
                                            <a href="{{ route('course.show', $bundle->slug) }}">{{ \Illuminate\Support\Str::limit($bundle->title, 60) }}</a>
                                        </h2>
                                        <p class="card-text">{{ $bundle->instructor ?? 'Horizons Faculty' }}</p>
                                        @if($bundleDescription)
                                            <p class="card-text text-muted small flex-grow-1">{{ $bundleDescription }}</p>
                                        @else
                                            <p class="card-text text-muted small flex-grow-1">{{ __('Discover the complete learning path inside this bundle.') }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                            <div class="card-price text-black font-weight-bold mb-0">
                                                @if($bundlePrice !== null)
                                                    ${{ number_format($bundlePrice, 2) }}
                                                @else
                                                    {{ __('Included') }}
                                                @endif
                                                @if($hasBundleDiscount && $bundleOldPrice)
                                                    <span class="before-price font-weight-medium">${{ number_format($bundleOldPrice, 2) }}</span>
                                                @endif
                                                @if($bundlePricing->badge)
                                                    <span class="badge badge-warning ms-2">{{ $bundlePricing->badge }}</span>
                                                @endif
                                            </div>
                                            <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="{{ __('Explore bundle') }}">
                                                <i class="la la-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div class="tooltip_templates">
                                    <div id="{{ $bundleTooltipId }}">
                                        <div class="card card-item mb-0">
                                            <div class="card-body">
                                                <p class="card-text pb-2 mb-0">
                                                    By <a href="{{ route('course.show', $bundle->slug) }}">{{ $bundle->instructor ?? 'Horizons Faculty' }}</a>
                                                </p>
                                                <h5 class="card-title pb-1">
                                                    <a href="{{ route('course.show', $bundle->slug) }}">{{ $bundle->title }}</a>
                                                </h5>
                                                <div class="d-flex flex-wrap align-items-center pb-1">
                                                    <h6 class="ribbon fs-14 me-2 mb-1">{{ $bundleType }}</h6>
                                                    @if($bundleUpdatedLabel)
                                                        <p class="text-success fs-14 font-weight-medium mb-1">
                                                            Updated <span class="font-weight-bold ps-1">{{ $bundleUpdatedLabel }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                                <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex flex-wrap align-items-center fs-14">
                                                    <li>{{ $bundleDurationLabel }}</li>
                                                    <li>{{ $bundleEffortLabel }}</li>
                                                    <li>{{ $bundleFormatLabel }}</li>
                                                </ul>
                                                @if($bundleTooltipDescription)
                                                    <p class="card-text pt-1 fs-14 lh-22">
                                                        {{ $bundleTooltipDescription }}
                                                    </p>
                                                @endif
                                                <ul class="generic-list-item fs-14 py-3">
                                                    <li>
                                                        <i class="la la-check me-1 text-black"></i> {{ $bundleLevelLabel }}
                                                    </li>
                                                    <li>
                                                        <i class="la la-check me-1 text-black"></i> {{ __('Flexible online delivery') }}
                                                    </li>
                                                    <li>
                                                        <i class="la la-check me-1 text-black"></i> {{ __('Access multiple tracks') }}
                                                    </li>
                                                </ul>
                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                    @auth
                                                        <form action="{{ route('cart.add', $bundle->id) }}" method="POST" class="flex-grow-1 me-1">
                                                            @csrf
                                                            <button type="submit" class="btn theme-btn w-100">
                                                                <i class="la la-shopping-cart me-1 fs-18"></i> {{ __('Add to Cart') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn theme-btn flex-grow-1 me-1">
                                                            <i class="la la-lock me-1 fs-18"></i> {{ __('Login to enroll') }}
                                                        </a>
                                                    @endauth
                                                    <a href="{{ route('course.show', $bundle->slug) }}" class="icon-element icon-element-sm shadow-sm" title="{{ __('View details') }}">
                                                        <i class="la la-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($bundlePaginator)
                            <div class="text-center mt-4">
                                {{ $bundlePaginator->onEachSide(1)->links('frontend.components.pagination') }}
                            </div>
                        @endif
                    @endif

                    @unless($showBundlesOnly)
                    <div class="section-heading d-flex flex-wrap align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="section__title fs-20 mb-0">Online Courses</h5>
                            <!--<p class="section__desc mb-0">In-demand skills taught by seasoned practitioners.</p>-->
                        </div>
                        <span class="fs-14 text-muted">{{ $all_courses->total() }} courses</span>
                    </div>

                    <div class="row">
                        @forelse($all_courses as $course)
                            @php
                                $placeholderImage = asset('frontend/assets/images/img-loading.png');
                                $rawImage = $course->image;
                                if ($rawImage && !\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                                    $rawImage = asset($rawImage);
                                }
                                $courseImage = $rawImage ?: $placeholderImage;
                                $pricing = \App\Services\CampaignService::pricingForCourse($course);
                                $currentPrice = $pricing->sale_price;
                                $oldPrice = $pricing->strike_price;
                                $hasDiscount = $pricing->has_discount || ($oldPrice && $currentPrice !== null && $oldPrice > $currentPrice);
                                $discountPercentage = ($hasDiscount && $oldPrice && $currentPrice !== null && $oldPrice > 0)
                                    ? max(1, round((($oldPrice - $currentPrice) / $oldPrice) * 100))
                                    : null;
                                $typeLabel = $course->type ? ucfirst($course->type) : 'Premium';
                                $tooltipId = 'tooltip-premium-course-' . $course->id;
                                $description = \Illuminate\Support\Str::limit(strip_tags($course->short_description ?? ''), 110);
                                $tooltipDescription = \Illuminate\Support\Str::limit(strip_tags($course->short_description ?: $course->long_description ?? ''), 220);
                                $updatedLabel = optional($course->updated_at)->format('F Y');
                                $durationLabel = $course->duration ?: 'Self-paced';
                                $effortLabel = $course->effort ?: 'Flexible schedule';
                                $formatLabel = $course->format ?: 'Online learning';
                                $levelLabel = $course->questions
                                    ? $course->questions . ' practice questions'
                                    : 'All learners welcome';
                                $categoryName = data_get($course, 'category.name') ?? __('Featured course');
                                $courseUrl = route('course.show', $course->slug);
                                $instructor = $course->instructor ?? 'Horizons Faculty';
                            @endphp
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="card card-item card-preview h-100" data-tooltip-content="#{{ $tooltipId }}">
                                    <div class="card-image">
                                        <a href="{{ $courseUrl }}" class="d-block">
                                            <img class="card-img-top lazy" src="{{ $placeholderImage }}" data-src="{{ $courseImage }}" alt="{{ $course->title }}">
                                        </a>
                                        <div class="course-badge-labels">
                                            <div class="course-badge">{{ $typeLabel }}</div>
                                            @if($discountPercentage)
                                                <div class="course-badge blue">-{{ $discountPercentage }}%</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <span class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $categoryName }}</span>
                                        <h2 class="card-title">
                                            <a href="{{ $courseUrl }}">{{ \Illuminate\Support\Str::limit($course->title, 60) }}</a>
                                        </h2>
                                        <p class="card-text">{{ $instructor }}</p>
                                        @if($description)
                                            <p class="card-text text-muted small flex-grow-1">{{ $description }}</p>
                                        @else
                                            <p class="card-text text-muted small flex-grow-1">{{ __('Discover the full curriculum and admission requirements.') }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                            <div class="card-price text-black font-weight-bold mb-0">
                                                @if($currentPrice !== null)
                                                    ${{ number_format($currentPrice, 2) }}
                                                @else
                                                    {{ __('Contact us') }}
                                                @endif
                                                @if($hasDiscount && $oldPrice)
                                                    <span class="before-price font-weight-medium">${{ number_format($oldPrice, 2) }}</span>
                                                @endif
                                                @if($pricing->badge)
                                                    <span class="badge badge-warning ms-2">{{ $pricing->badge }}</span>
                                                @endif
                                            </div>
                                            <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="{{ __('Explore course') }}">
                                                <i class="la la-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tooltip_templates">
                                <div id="{{ $tooltipId }}">
                                    <div class="card card-item mb-0">
                                        <div class="card-body">
                                            <p class="card-text pb-2 mb-0">
                                                By <a href="{{ $courseUrl }}">{{ $instructor }}</a>
                                            </p>
                                            <h5 class="card-title pb-1">
                                                <a href="{{ $courseUrl }}">{{ $course->title }}</a>
                                            </h5>
                                            <div class="d-flex flex-wrap align-items-center pb-1">
                                                <h6 class="ribbon fs-14 me-2 mb-1">{{ $typeLabel }}</h6>
                                                @if($updatedLabel)
                                                    <p class="text-success fs-14 font-weight-medium mb-1">
                                                        Updated <span class="font-weight-bold ps-1">{{ $updatedLabel }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                            <ul class="generic-list-item generic-list-item-bullet generic-list-item--bullet d-flex flex-wrap align-items-center fs-14">
                                                <li>{{ $durationLabel }}</li>
                                                <li>{{ $effortLabel }}</li>
                                                <li>{{ $formatLabel }}</li>
                                            </ul>
                                            @if($tooltipDescription)
                                                <p class="card-text pt-1 fs-14 lh-22">
                                                    {{ $tooltipDescription }}
                                                </p>
                                            @endif
                                            <ul class="generic-list-item fs-14 py-3">
                                                <li>
                                                    <i class="la la-check me-1 text-black"></i> {{ $levelLabel }}
                                                </li>
                                                <li>
                                                    <i class="la la-check me-1 text-black"></i> {{ $categoryName }}
                                                </li>
                                                <li>
                                                    <i class="la la-check me-1 text-black"></i> {{ __('Flexible online delivery') }}
                                                </li>
                                            </ul>
                                            <div class="d-flex justify-content-between align-items-center gap-3">
                                                @auth
                                                    @if($course->type === 'free' && $course->link)
                                                        <a href="{{ $course->link }}" class="btn theme-btn flex-grow-1 me-1" target="_blank" rel="noopener">
                                                            <i class="la la-play-circle me-1 fs-18"></i> {{ __('Start for free') }}
                                                        </a>
                                                    @else
                                                        <form action="{{ route('cart.add', $course->id) }}" method="POST" class="flex-grow-1 me-1">
                                                            @csrf
                                                            <button type="submit" class="btn theme-btn w-100">
                                                                <i class="la la-shopping-cart me-1 fs-18"></i> {{ __('Add to Cart') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <a href="{{ route('login') }}" class="btn theme-btn flex-grow-1 me-1">
                                                        <i class="la la-lock me-1 fs-18"></i> {{ __('Login to enroll') }}
                                                    </a>
                                                @endauth
                                                <a href="{{ $courseUrl }}" class="icon-element icon-element-sm shadow-sm" title="{{ __('View details') }}">
                                                    <i class="la la-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-4">
                                    <strong>No courses found.</strong> Try adjusting your filters or search phrase.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="text-center mt-4">
                        {{ $all_courses->onEachSide(1)->links('frontend.components.pagination') }}
                    </div>
                    @endunless
                </div>

                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="card card-item mb-4">
                            <div class="card-body">
                                <h3 class="widget-title border-bottom pb-3 mb-4">Search Field</h3>
                                <form method="GET" action="{{ $listingRoute }}" class="form-box d-flex align-items-center">
                                    <input type="text" class="form-control me-2" name="search" placeholder="{{ $showBundlesOnly ? 'Search programs' : 'Search courses' }}" value="{{ $search }}">
                                    <input type="hidden" name="category" value="{{ optional($activeCategory)->slug }}">
                                    <input type="hidden" name="subcategory" value="{{ optional($activeSubcategory)->slug }}">
                                    <input type="hidden" name="child" value="{{ optional($activeChildCategory)->slug }}">
                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                    <input type="hidden" name="price_min" value="{{ $selectedMin }}">
                                    <input type="hidden" name="price_max" value="{{ $selectedMax }}">
                                    <button class="btn theme-btn theme-btn-sm" type="submit">
                                        <span class="la la-search"></span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if(!$showBundlesOnly && $categories->isNotEmpty())
                            <div class="card card-item mb-4">
                                <div class="card-body">
                                    <h3 class="widget-title border-bottom pb-3 mb-4">Categories</h3>
                                    <ul class="generic-list-item">
                                        @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('premium-courses', $buildQuery(['category' => $category->slug, 'subcategory' => null, 'child' => null, 'page' => null])) }}" class="{{ optional($activeCategory)->id === $category->id ? 'text-primary font-weight-bold' : '' }}">
                                                    {{ $category->name }}
                                                    <span class="badge badge-light ms-2">{{ $category->premium_courses_count ?? 0 }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if(!$showBundlesOnly && $subcategories->isNotEmpty())
                            <div class="card card-item mb-4">
                                <div class="card-body">
                                    <h3 class="widget-title border-bottom pb-3 mb-4">{{ $activeCategory->name }} Tracks</h3>
                                    <ul class="generic-list-item">
                                        @foreach($subcategories as $subcategory)
                                            <li>
                                                <a href="{{ route('premium-courses', $buildQuery(['subcategory' => $subcategory->slug, 'child' => null, 'page' => null])) }}" class="{{ optional($activeSubcategory)->id === $subcategory->id ? 'text-primary font-weight-bold' : '' }}">
                                                    {{ $subcategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if(!$showBundlesOnly && $childCategories->isNotEmpty())
                            <div class="card card-item mb-4">
                                <div class="card-body">
                                    <h3 class="widget-title border-bottom pb-3 mb-4">{{ $activeSubcategory->name }} Focus</h3>
                                    <ul class="generic-list-item">
                                        @foreach($childCategories as $childCategory)
                                            <li>
                                                <a href="{{ route('premium-courses', $buildQuery(['child' => $childCategory->slug, 'page' => null])) }}" class="{{ optional($activeChildCategory)->id === $childCategory->id ? 'text-primary font-weight-bold' : '' }}">
                                                    {{ $childCategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="card card-item mb-4">
                            <div class="card-body">
                                <h3 class="widget-title border-bottom pb-3 mb-4">Price filter</h3>
                                <form method="GET" action="{{ $listingRoute }}">
                                    <div class="row mb-3">
                                        <div class="col-6 mb-2">
                                            <label class="form-label fs-12 text-uppercase text-muted">Min ($)</label>
                                            <input type="number" step="0.01" class="form-control" name="price_min" value="{{ $selectedMin ?? $priceFloor }}">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="form-label fs-12 text-uppercase text-muted">Max ($)</label>
                                            <input type="number" step="0.01" class="form-control" name="price_max" value="{{ $selectedMax ?? $priceCeil }}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="search" value="{{ $search }}">
                                    <input type="hidden" name="category" value="{{ optional($activeCategory)->slug }}">
                                    <input type="hidden" name="subcategory" value="{{ optional($activeSubcategory)->slug }}">
                                    <input type="hidden" name="child" value="{{ optional($activeChildCategory)->slug }}">
                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                    <button class="btn theme-btn w-100" type="submit">Apply filter</button>
                                </form>
                            </div>
                        </div>

                        @if($hasActiveFilters)
                            <div class="card card-item mb-4 border border-warning">
                                <div class="card-body">
                                    <h3 class="widget-title border-bottom pb-2 mb-3">Active filters</h3>
                                    <div class="d-flex flex-wrap mb-3">
                                        @if($search !== '')
                                            <span class="badge badge-light text-dark border mr-2 mb-2">Search: {{ $search }}</span>
                                        @endif
                                        @if($activeCategory)
                                            <span class="badge badge-light text-dark border mr-2 mb-2">Category: {{ $activeCategory->name }}</span>
                                        @endif
                                        @if($activeSubcategory)
                                            <span class="badge badge-light text-dark border mr-2 mb-2">Subcategory: {{ $activeSubcategory->name }}</span>
                                        @endif
                                        @if($activeChildCategory)
                                            <span class="badge badge-light text-dark border mr-2 mb-2">Focus: {{ $activeChildCategory->name }}</span>
                                        @endif
                                        @if($selectedMin)
                                            <span class="badge badge-light text-dark border mr-2 mb-2">Min: ${{ number_format((float) $selectedMin, 2) }}</span>
                                        @endif
                                        @if($selectedMax)
                                            <span class="badge badge-light text-dark border mr-2 mb-2">Max: ${{ number_format((float) $selectedMax, 2) }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ $listingRoute }}" class="btn btn-sm btn-outline-warning w-100">Clear all filters</a>
                                </div>
                            </div>
                        @endif

                        <div class="card card-item">
                            <div class="card-body text-center">
                                <h3 class="widget-title mb-3">Need admission support?</h3>
                                <p class="text-muted mb-4">Talk with our advisors to personalize your course roadmap.</p>
                                <a href="{{ route('consultation.step1') }}" class="theme-btn w-100">Book a consultation</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

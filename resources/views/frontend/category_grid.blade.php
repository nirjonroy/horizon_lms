@extends('frontend.app')
@php
    $primaryItems = $primaryItems ?? collect();
    $courses = $courses ?? null;
    $search = trim((string) ($search ?? ''));
    $heroBackground = $heroImage
        ? "background-image: linear-gradient(180deg, rgba(13,25,56,.8), rgba(13,25,56,.8)), url('{$heroImage}'); background-size: cover; background-position: center;"
        : 'background-color: #0d1938;';
    $heroStyles = trim($heroBackground . ' padding: 40px 0; min-height: 150px;');
    $fallbackImages = [
        asset('frontend/assets/images/img8.jpg'),
        asset('frontend/assets/images/img9.jpg'),
        asset('frontend/assets/images/img10.jpg'),
        asset('frontend/assets/images/img11.jpg'),
        asset('frontend/assets/images/img12.jpg'),
    ];
    $fallbackCount = count($fallbackImages);
    $normalizeImage = function ($path, $index = 0) use ($fallbackImages, $fallbackCount) {
        if (! $path) {
            return $fallbackImages[$fallbackCount ? $index % $fallbackCount : 0];
        }
        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };
    $displayCount = $courses ? $courses->total() : ($primaryItemsType ? $primaryItems->count() : 0);
    $itemLabel = $courses
        ? \Illuminate\Support\Str::plural('course', $displayCount)
        : \Illuminate\Support\Str::plural('category', $displayCount);
    $seoData = $seo ?? [];
    $seoTitle = $seoData['title'] ?? ($pageTitle . ' | ' . config('app.name', 'Horizons Unllimite'));
    $seoDescription = $seoData['description'] ?? $pageSummary;
    $seoKeywords = $seoData['keywords'] ?? '';
    $seoMetaImage = $seoData['meta_image'] ?? null;
    $seoAuthor = $seoData['author'] ?? config('app.name', 'Horizons Unllimite');
    $seoPublisher = $seoData['publisher'] ?? $seoAuthor;
    $seoCopyright = $seoData['copyright'] ?? $seoAuthor;
    $seoSiteName = $seoData['site_name'] ?? config('app.name', 'Horizons Unllimite');
    $activeDescription = $activeDescription ?? null;
@endphp
@section('title', $seoTitle)
@section('seos')
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="author" content="{{ $seoAuthor }}">
    <meta name="publisher" content="{{ $seoPublisher }}">
    <meta name="copyright" content="{{ $seoCopyright }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    @if($seoMetaImage)
        <meta property="og:image" content="{{ $seoMetaImage }}">
        <meta name="twitter:image" content="{{ $seoMetaImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:site" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
@endsection
@section('content')
    <style>
        .category-topics-carousel {
            margin-bottom: 40px;
        }

        .category-topic-slide {
            padding: 8px 10px;
            height: 100%;
        }

        .category-topic-chip {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 20px;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            background-color: #fff;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            text-decoration: none;
            color: #111827;
            min-height: 110px;
            height: 100%;
            gap: 6px;
        }

        .category-topic-chip:hover {
            box-shadow: 0 12px 30px rgba(13, 25, 56, 0.08);
            transform: translateY(-2px);
        }

        .category-topic-chip .topic-label {
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .category-topic-chip .topic-desc {
            display: block;
            font-size: 13px;
            color: #6b7280;
        }

        .category-topic-chip .topic-meta {
            font-size: 13px;
            color: #2563eb;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 0;
            margin-top: auto;
        }

        .description-content p {
            margin-bottom: 1rem;
        }
        
    </style>
    <section class="breadcrumb-area section-padding img-bg-2" style="{{ $heroStyles }}">
        <div class="overlay"></div>
        <div class="container">
            <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
                <div class="section-heading mb-3 mb-lg-0">
                    <h1 class="section__title text-white">{{ $pageTitle }} Courses</h1>
                    @if($pageSummary)
                        <p class="section__desc text-white-50 pt-2">{{ $pageSummary }}</p>
                    @endif
                </div>
                <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center mb-0">
                    @foreach($breadcrumbs as $crumb)
                        @if(!empty($crumb['url']))
                            <li><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                        @else
                            <li>{{ $crumb['label'] }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="course-area " style="margin-top: 20px !important">
        <div class="container">
            <div class="filter-bar mb-4">
                <div class="filter-bar-inner d-flex flex-wrap align-items-center justify-content-between">
                    <p class="fs-14 mb-3 mb-lg-0">
                        We found <span class="text-black">{{ $displayCount }}</span> {{ $itemLabel }} available for you
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
                            <div class="input-group input-group-sm">
                                <input
                                    type="search"
                                    name="search"
                                    value="{{ $search }}"
                                    class="form-control form--control"
                                    placeholder="Search courses" />
                                <button class="btn theme-btn theme-btn-sm" type="submit">Search</button>
                            </div>
                            @if($search !== '')
                                <a href="{{ request()->url() }}" class="btn theme-btn theme-btn-sm theme-btn-white">
                                    Clear
                                </a>
                            @endif
                        </form>
                        <a href="{{ route('premium-courses') }}" class="btn theme-btn theme-btn-sm theme-btn-white lh-28">
                            All Courses
                        </a>
                    </div>
                </div>
            </div>

            @if($primaryItemsType && $primaryItems->isNotEmpty())
                @php
                    $sectionHeading = $primaryItemsType === 'child' ? 'Focus areas' : 'Popular topics';
                    $sectionSubheading = $primaryItemsType === 'child'
                        ? 'Pick a focus area to dive deeper into this track.'
                        : 'Choose a topic to keep exploring premium courses.';
                @endphp
                <div class="section-heading pb-3">
                    <h3 class="fs-24 font-weight-semi-bold text-capitalize mb-1">
                        {{ $sectionHeading }}
                    </h3>
                    <!--<p class="text-gray">-->
                    <!--    {{ $sectionSubheading }}-->
                    <!--</p>-->
                </div>

                <div class="category-topics-carousel owl-action-styled owl--action-styled">
                    @foreach($primaryItems as $item)
                        @php
                            $courseCount = (int) ($item->premium_courses_count ?? $item->courses_count ?? 0);
                            $topicUrl = $primaryItemsType === 'child'
                                ? route('courses.child.show', [
                                    'category' => optional($category)->slug,
                                    'subcategory' => optional($subcategory)->slug,
                                    'childCategory' => $item->slug,
                                ])
                                : route('courses.subcategory.show', [
                                    'category' => optional($category)->slug,
                                    'subcategory' => $item->slug,
                                ]);
                            $topicSummary = $item->description
                                ? \Illuminate\Support\Str::limit(strip_tags($item->description), 90)
                                : null;
                        @endphp
                        <div class="category-topic-slide">
                            <a href="{{ $topicUrl }}" class="category-topic-chip">
                                <span class="topic-label">{{ $item->name }}</span>
                                <!-- @if($topicSummary)
                                    <span class="topic-desc">{{ $topicSummary }}</span>
                                @endif -->
                                <span class="topic-meta">
                                    {{ $courseCount }} {{ \Illuminate\Support\Str::plural('course', $courseCount) }}
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>

                @if($courses && $courses->count())
                    <div class="section-divider my-5"></div>
                @endif
            @endif

            @if($courses && $courses->count())
                <div class="row g-4">
                    @foreach($courses as $course)
                        @php
                            $placeholderImage = asset('frontend/assets/images/img-loading.png');
                            $rawImage = $course->image;
                            if ($rawImage && !\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                                $rawImage = asset($rawImage);
                            }
                            $image = $rawImage ?: $placeholderImage;
                            $courseUrl = route('course.show', $course->slug);
                            $pricing = \App\Services\CampaignService::pricingForCourse($course);
                            $salePrice = $pricing->sale_price;
                            $strikePrice = $pricing->strike_price;
                            $hasDiscount = $pricing->has_discount || ($strikePrice && $salePrice !== null && $strikePrice > $salePrice);
                            $discountPercent = ($hasDiscount && $strikePrice && $salePrice !== null && $strikePrice > 0)
                                ? max(1, round((($strikePrice - $salePrice) / $strikePrice) * 100))
                                : null;
                            $typeLabel = $course->type ? ucfirst($course->type) : ($course->format ?? 'Premium');
                            $tooltipId = 'tooltip-category-course-' . $course->id;
                            $description = \Illuminate\Support\Str::limit(strip_tags($course->short_description ?? ''), 110);
                            $tooltipDescription = \Illuminate\Support\Str::limit(strip_tags($course->short_description ?: $course->long_description ?? ''), 220);
                            $updatedLabel = optional($course->updated_at)->format('F Y');
                            $durationLabel = $course->duration ?: 'Self-paced';
                            $effortLabel = $course->effort ?: 'Flexible schedule';
                            $formatLabel = $course->format ?: 'Online learning';
                            $levelLabel = $course->questions
                                ? $course->questions . ' practice questions'
                                : 'All learners welcome';
                            $categoryName = optional($category)->name ?? __('Featured course');
                            $instructor = $course->instructor ?? 'Horizons Faculty';
                        @endphp
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card card-item card-preview h-100" data-tooltip-content="#{{ $tooltipId }}">
                                <div class="card-image">
                                    <a href="{{ $courseUrl }}" class="d-block">
                                        <img class="card-img-top lazy" src="{{ $placeholderImage }}" data-src="{{ $image }}" alt="{{ $course->title }}">
                                    </a>
                                    <div class="course-badge-labels">
                                        <div class="course-badge">{{ $typeLabel }}</div>
                                        @if($discountPercent)
                                            <div class="course-badge blue">-{{ $discountPercent }}%</div>
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
                                            @if($salePrice !== null)
                                                ${{ number_format($salePrice, 2) }}
                                            @else
                                                {{ __('Contact us') }}
                                            @endif
                                            @if($hasDiscount && $strikePrice)
                                                <span class="before-price font-weight-medium">${{ number_format($strikePrice, 2) }}</span>
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
                                            <span class="ribbon fs-14 me-2 mb-1">{{ $typeLabel }}</span>
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
                    @endforeach
                </div>
                <div class="pt-4">
                    {{ $courses->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            @elseif(! $primaryItemsType || $primaryItems->isEmpty())
                <div class="text-center py-5">
                    <h4 class="mb-2">No premium courses found</h4>
                    <p class="text-muted mb-3">
                        Check back soon—we are curating more content for this category.
                    </p>
                    <a href="{{ route('premium-courses') }}" class="btn theme-btn">
                        Browse All Courses
                    </a>
                </div>
            @endif

            @if(!empty($activeDescription))
                <div class="section-divider my-5"></div>
                <div class="card card-item shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ $pageTitle }} Overview</h4>
                        <div class="description-content text-gray">{!! $activeDescription !!}</div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

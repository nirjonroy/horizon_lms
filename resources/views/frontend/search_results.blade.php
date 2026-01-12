@extends('frontend.app')

@section('title', 'Search results for "' . e($search) . '"')

@section('content')
@php
    $normalizeMedia = function ($path, $fallback = null) {
        if ($path === null || trim((string) $path) === '') {
            return $fallback ? asset($fallback) : null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) ? $path : asset($path);
    };
@endphp
<section class="bg-light py-5">
    <div class="container">
        <div class="bg-white shadow-sm rounded-3 p-4 p-md-5 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div>
                    <p class="text-muted mb-1">Search results</p>
                    <h1 class="h4 mb-0">
                        {{ number_format($totalResults) }} match{{ $totalResults === 1 ? '' : 'es' }} for
                        “<span class="fw-semibold">{{ $search }}</span>”
                    </h1>
                </div>
                <form method="GET" action="{{ route('search') }}" class="w-100 w-md-auto">
                    <div class="input-group">
                        <input
                            type="search"
                            name="search"
                            class="form-control form--control"
                            value="{{ $search }}"
                            placeholder="Search again"
                            required
                        />
                        <button class="btn btn-primary" type="submit">
                            <i class="la la-search me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
            <p class="mt-3 mb-0 text-muted small">
                Tip: you can search for a premium course, a tuition fee plan, or a partner university from one place.
            </p>
        </div>

        @if ($totalResults === 0)
            <div class="bg-white text-center border rounded-3 p-5">
                <h2 class="h4 mb-3">No matches yet</h2>
                <p class="text-muted mb-4">Try another keyword or explore all of our courses and partner universities.</p>
                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                    <a href="{{ route('premium-courses') }}" class="btn btn-primary">
                        Browse premium courses
                    </a>
                    <a href="{{ route('home.index') }}" class="btn btn-outline-secondary">
                        Explore universities & partners
                    </a>
                </div>
            </div>
        @endif

        @if ($premiumCourses->isNotEmpty())
            <div class="bg-white shadow-sm rounded-3 p-4 p-md-5 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Premium Courses</p>
                        <h2 class="h5 mb-0">Courses that match “{{ $search }}”</h2>
                    </div>
                    <a class="btn btn-link text-decoration-none" href="{{ route('premium-courses', ['search' => $search]) }}">
                        View all related courses <span class="la la-arrow-right"></span>
                    </a>
                </div>
                <div class="row g-3">
                    @foreach ($premiumCourses as $course)
                        @php
                            $courseImage = $normalizeMedia($course->image, 'frontend/assets/images/course-placeholder.jpg');
                            $description = \Illuminate\Support\Str::limit(strip_tags($course->short_description), 120);
                            $pricing = \App\Services\CampaignService::pricingForCourse($course);
                            $priceLabel = is_null($pricing->sale_price) || $pricing->sale_price <= 0
                                ? __('Free access')
                                : '$' . number_format($pricing->sale_price, 2);
                            $strikePrice = $pricing->strike_price;
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('course.show', $course->slug) }}" class="text-decoration-none h-100 d-flex">
                                <div class="border rounded-3 p-3 w-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="rounded-2 bg-light overflow-hidden" style="width:70px;height:70px;">
                                            @if ($courseImage)
                                                <img src="{{ $courseImage }}" alt="{{ $course->title }}" class="img-fluid h-100 w-100" style="object-fit: cover;">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-uppercase small text-muted mb-1">{{ ucfirst($course->type ?? 'premium') }}</p>
                                            <h3 class="h6 text-dark mb-0">{{ $course->title }}</h3>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-3">{{ $description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold text-primary">
                                            {{ $priceLabel }}
                                            @if($strikePrice)
                                                <span class="before-price ms-1">${{ number_format($strikePrice, 2) }}</span>
                                            @endif
                                        </span>
                                        <span class="text-primary fw-semibold small">
                                            View details <i class="la la-arrow-right ms-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($onlineFees->isNotEmpty())
            <div class="bg-white shadow-sm rounded-3 p-4 p-md-5 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Tuition & Online Fees</p>
                        <h2 class="h5 mb-0">Payment plans that match “{{ $search }}”</h2>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Degree</th>
                                <th>Total Fee</th>
                                <th>Discounted / Yearly</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($onlineFees as $fee)
                                <tr>
                                    <td>
                                        <p class="mb-0 fw-semibold">{{ $fee->program }}</p>
                                        <small class="text-muted">{{ $fee->short_name }}</small>
                                    </td>
                                    <td>{{ optional($fee->feesCategory)->name ?? 'N/A' }}</td>
                                    <td>${{ number_format((float) $fee->total_fee, 2) }}</td>
                                    <td>
                                        @if ((float) $fee->yearly > 0)
                                            <span class="text-success fw-semibold">
                                                ${{ number_format((float) $fee->yearly, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Same as total</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $ctaUrl = $fee->link ?: route('apply.now', $fee->slug ?? null);
                                        @endphp
                                        <a
                                            href="{{ $ctaUrl }}"
                                            class="btn btn-sm btn-primary"
                                            @if ($fee->link) target="_blank" rel="noopener" @endif
                                        >
                                            Apply now
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($universities->isNotEmpty())
            <div class="bg-white shadow-sm rounded-3 p-4 p-md-5 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Where to Study</p>
                        <h2 class="h5 mb-0">Partner universities that match “{{ $search }}”</h2>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($universities as $university)
                        @php
                            $universityImage = $normalizeMedia($university->slider1);
                            $uniDescription = \Illuminate\Support\Str::limit(strip_tags($university->short_description), 140);
                        @endphp
                        <div class="col-md-6">
                            <div class="border rounded-3 h-100 p-3 d-flex flex-column">
                                <div class="d-flex gap-3 mb-3">
                                    <div class="rounded-2 bg-light overflow-hidden" style="width:70px;height:70px;">
                                        @if ($universityImage)
                                            <img src="{{ $universityImage }}" alt="{{ $university->name }}" class="img-fluid h-100 w-100" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="h6 mb-1">{{ $university->name }}</h3>
                                        <p class="text-muted small mb-0">{{ $uniDescription }}</p>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('where.to.study', $university->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                        Explore {{ $university->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

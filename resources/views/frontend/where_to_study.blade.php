@extends('frontend.app')

@section('title', $studies->meta_title ?? $studies->name)
@section('seos')
    @php
        $SeoSettings = DB::table('seo_settings')->where('id', 1)->first();
        $siteInfo = DB::table('site_information')->first();
        $keywordsArray = json_decode($studies->keywords, true);
        $metaTitle = $studies->meta_title ?: ($SeoSettings->seo_title ?? $studies->name);
        $metaDescription = $studies->meta_description ?: ($SeoSettings->seo_description ?? '');
        $metaAuthor = data_get($studies, 'meta_author');
        $metaPublisher = data_get($studies, 'meta_publisher');
        $seoAuthor = $metaAuthor ?? ($SeoSettings->author ?? ($siteInfo->title ?? config('app.name')));
        $seoPublisher = $metaPublisher ?? ($SeoSettings->publisher ?? $seoAuthor);
    @endphp

    <meta charset="UTF-8">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="title" content="{{ $metaTitle }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ isset($keywordsArray) ? implode(', ', $keywordsArray) : '' }}" />
    <meta name="author" content="{{ $seoAuthor }}">
    <meta name="publisher" content="{{ $seoPublisher }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $metaTitle }}">
    <meta property="og:image" content="{{ asset($studies->slider1) }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="628">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
@endsection

@section('content')
<section class="bg-light border-bottom">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge bg-primary-subtle text-primary mb-3">Where to Study</span>
                <h1 class="display-5 fw-bold text-primary mb-3">{{ $studies->name }}</h1>
                <div class="text-muted mb-4">{!! $studies->short_description !!}</div>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('consultation.step1') }}" class="btn theme-btn">Book Consultation</a>
                    <a href="{{ route('apply.now') }}" class="btn btn-outline-primary">Apply Now</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow">
                    <img src="{{ asset($studies->slider1) }}" alt="{{ $studies->name }}" class="w-100 h-100 object-fit-cover">
                </div>
            </div>
        </div>
    </div>
</section>

@if($latest_course->count())
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-secondary-subtle text-secondary mb-2">Spotlight</span>
            <h2 class="fw-bold text-primary">Popular Programs</h2>
            <p class="text-muted mb-0">High-demand courses students are enrolling in right now.</p>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($latest_course as $item)
                @php
                    $hasDiscount = (float) $item->yearly > 0 && (float) $item->total_fee > 0;
                    $discountPercent = $hasDiscount ? (int) ((($item->total_fee - $item->yearly) / $item->total_fee) * 100) : null;
                    $programUrl = $item->slug
                        ? route('university.program.show', ['slug' => $studies->slug, 'program' => $item->slug])
                        : null;
                    $applyUrl = $item->link ?: route('apply.now', $item->slug ?? null);
                    $programSummary = $item->short_description
                        ? \Illuminate\Support\Str::limit(strip_tags($item->short_description), 140)
                        : 'Explore this program and connect with our admissions advisors.';
                @endphp
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm position-relative rounded-4">
                        @if($hasDiscount)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-3 rounded-pill">
                                Save {{ $discountPercent }}%
                            </span>
                        @endif
                        <div class="card-body">
                            <h3 class="h5 text-primary">
                                @php
                                    $programTitle = $item->short_name ?: $item->program;
                                @endphp
                                @if($programUrl)
                                    <a href="{{ $programUrl }}" class="text-decoration-none">{{ $programTitle }}</a>
                                @else
                                    {{ $programTitle }}
                                @endif
                            </h3>
                            <div class="text-muted mb-3 small">{{ $programSummary }}</div>
                            <div class="d-flex align-items-baseline gap-3">
                                <span class="fs-4 fw-bold text-success">${{ number_format($hasDiscount ? $item->yearly : $item->total_fee, 2) }}</span>
                                @if($hasDiscount)
                                    <span class="text-muted text-decoration-line-through">${{ number_format($item->total_fee, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                            <a href="{{ $applyUrl }}" class="btn btn-outline-primary w-100" @if($item->link) target="_blank" rel="noopener" @endif>Apply Now</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-5 bg-light">
    <div class="container">
        @foreach($categories as $category)
            <div class="card border-0 shadow-sm mb-4 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                        <div>
                            <p class="text-uppercase text-muted mb-1">{{ $category->name }}</p>
                            <h3 class="h4 text-primary mb-0">{{ $studies->name }} {{ $category->name }} Programs</h3>
                        </div>
                        <a href="{{ route('consultation.step1') }}" class="btn btn-sm btn-outline-secondary mt-3 mt-md-0">
                            Talk to an advisor
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Program</th>
                                    <th scope="col">Tuition</th>
                                    <th scope="col">Discounted</th>
                                    <th scope="col" style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->onlineFees as $fee)
                                    @php
                                        $feeHasDiscount = (float) $fee->yearly > 0 && (float) $fee->total_fee > 0;
                                        $programDiscount = $feeHasDiscount ? (int)((($fee->total_fee - $fee->yearly) / $fee->total_fee) * 100) : null;
                                        $programUrl = $fee->slug
                                            ? route('university.program.show', ['slug' => $studies->slug, 'program' => $fee->slug])
                                            : null;
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">
                                            @if($programUrl)
                                                <a href="{{ $programUrl }}" class="text-decoration-none">{{ $fee->program }}</a>
                                            @else
                                                {{ $fee->program }}
                                            @endif
                                        </td>
                                        <td>${{ number_format($fee->total_fee, 2) }}</td>
                                        <td>
                                            @if($feeHasDiscount)
                                                <span class="fw-semibold text-success">${{ number_format($fee->yearly, 2) }}</span>
                                                <span class="badge bg-success-subtle text-success ms-2">{{ $programDiscount }}% off</span>
                                            @else
                                                <span class="text-muted"></span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            @if($fee->link)
                                                <a href="{{ $fee->link }}" class="btn btn-sm theme-btn" target="_blank" rel="noopener">Apply Now</a>
                                            @else
                                                <a href="{{ route('apply.now', $fee->slug ?? null) }}" class="btn btn-sm theme-btn">Apply Now</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="py-5 text-white" style="background: linear-gradient(120deg, #001d42, #e6443c);">
    <div class="container text-center">
        <h2 class="fw-bold mb-3" style="color:white">Ready to Join {{ $studies->name }}?</h2>
        <p class="text-white-50 mb-4">Take the next step in your academic and professional journey with our world-class programs.</p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="{{ route('apply.now') }}" class="btn btn-light text-primary px-4">Apply Now</a>
            <a href="{{ route('consultation.step1') }}" class="btn btn-outline-light px-4">Book Consultancy</a>
        </div>
    </div>
</section>
@endsection

@extends('frontend.app')
@php
    $SeoSettings = \App\Models\SeoSetting::forPage('who-we-are');
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
    $rawMetaImage = optional($SeoSettings)->image ?: ($siteInfo->logo ?? null);
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
    $coreValues = [
        ['title' => 'Excellence', 'text' => 'Striving for the highest standards in education, guidance, and service delivery.'],
        ['title' => 'Integrity', 'text' => 'Operating with transparency, honesty, and accountability in everything we do.'],
        ['title' => 'Global Impact', 'text' => 'Connecting learners across borders with transformative educational opportunities.'],
        ['title' => 'Innovation', 'text' => 'Continuously evolving with cutting-edge technology and teaching methodologies.'],
        ['title' => 'Collaboration', 'text' => 'Partnering with leading institutions and industry experts to deliver exceptional outcomes.'],
        ['title' => 'Empowerment', 'text' => 'Inspiring students to take charge of their personal and professional growth.'],
        ['title' => 'Accessibility', 'text' => 'Making quality education and consulting services available to learners everywhere.'],
        ['title' => 'Inclusivity', 'text' => 'Embracing diversity and fostering an inclusive environment for all learners.'],
        ['title' => 'Student-Centricity', 'text' => 'Prioritizing the individual needs, goals, and success of our students and clients.'],
        ['title' => 'Career-Driven Skill Development', 'text' => 'Designing programs that enhance employability and career growth across diverse industries.'],
        ['title' => 'Accredited Global Partnerships', 'text' => 'Study with top universities in the UK and Europe while living in the Middle East—100% online.'],
        ['title' => 'Flexible & Affordable Learning', 'text' => 'Self-paced programs, monthly payment plans, and a 30-day money-back guarantee.'],
    ];
    $universities = DB::table('where_to_studies')->where('status', 1)->orderBy('name')->get();
@endphp

<section class="py-5 text-white" style="background:linear-gradient(120deg,#001d42,#0b2d63);">
    <div class="container text-center">
        <p class="text-uppercase small mb-2">About Us</p>
        <h1 class="display-5 fw-bold mb-3" style="color:#ffffff">Horizons Unlimited</h1>
        <p class="lead mb-0">Connecting the Middle East with globally accredited online education from top universities worldwide.</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <span class="badge bg-primary-subtle text-primary mb-3">Our Mission</span>
                        <h2 class="h4 fw-bold text-primary mb-3">Empowering learners everywhere</h2>
                        <p class="text-muted mb-0">To empower students and professionals worldwide by providing personalized, innovative, and accessible educational consulting and elite courses that foster academic excellence, career growth, and lifelong learning.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <span class="badge bg-primary-subtle text-primary mb-3">Our Vision</span>
                        <h2 class="h4 fw-bold text-primary mb-3">Leading global education online</h2>
                        <p class="text-muted mb-0">To be the global leader in online educational consulting and elite course provision, transforming lives through exceptional guidance, innovative learning solutions, and a commitment to educational equity.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-light text-primary border border-primary mb-2">Core Values</span>
            <h2 class="fw-bold">What Guides Us</h2>
            <p class="text-muted mb-0">Our work is anchored in values that reflect our promise to learners and partners across the globe.</p>
        </div>
        <div class="row g-4">
            @foreach($coreValues as $value)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 text-primary">{{ $value['title'] }}</h3>
                            <p class="text-muted mb-0">{{ $value['text'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-light text-primary border border-primary mb-2">Partners</span>
            <h2 class="fw-bold">Our University Network</h2>
            <p class="text-muted mb-0">We collaborate with prestigious institutions to bring you world-class education.</p>
        </div>
        <div class="row g-3">
            @foreach($universities as $university)
                <div class="col-6 col-md-3">
                    <div class="border rounded-3 bg-white text-center py-3 h-100 shadow-sm">
                        <a href="{{ route('where.to.study', $university->slug) }}" class="fw-semibold text-primary d-block small">{{ $university->name }}</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-primary">Premium Courses Designed for Your Life</h2>
                <p class="text-muted mb-4">Advance your skills with exclusive content, personalized coaching, and a flexible learning experience designed around your goals.</p>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><i class="la la-check-circle text-success me-2"></i>Monthly subscription plans</li>
                    <li class="mb-2"><i class="la la-check-circle text-success me-2"></i>30-day money-back guarantee</li>
                    <li class="mb-2"><i class="la la-check-circle text-success me-2"></i>Exclusive content and resources</li>
                    <li class="mb-2"><i class="la la-check-circle text-success me-2"></i>Personalized learning paths</li>
                </ul>
                <a href="{{ route('premium-courses') }}" class="btn theme-btn mt-2">Explore Premium Plans</a>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80" alt="Premium Courses" class="w-100 h-100 object-fit-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 text-white" style="background:linear-gradient(120deg,#0b2d63,#e6443c);">
    <div class="container text-center">
        <h2 class="fw-bold mb-3" style="color:#ffffff">Start Your Online Learning Journey</h2>
        <p class="mb-4 text-white-50">Join learners from UAE, Saudi Arabia, Qatar, Oman, and across the Middle East who are earning accredited UK, US, French, and Swiss degrees online.</p>
        <a href="{{ route('apply.now') }}" class="btn btn-light text-primary px-4">Apply Now</a>
    </div>
</section>
@endsection

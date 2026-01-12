@extends('frontend.app')
@php
    $SeoSettings = \App\Models\SeoSetting::forPage('contact-us');
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
    $info = $info ?? $siteInfo;
    $address = $info->address ?? '14 Pidgon Hill Dr. Ste.160, Sterling, VA 20165';
    $phoneDisplay = $info->mobile1 ?? '(833) 33-STUDY';
    $emailDisplay = $info->email1 ?? 'info@thehorizonsunlimited.com';
    $mapLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
    $telLink = 'tel:' . preg_replace('/[^0-9+]/', '', $phoneDisplay);
    $emailLink = 'mailto:' . $emailDisplay;
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
<section class="py-5 text-white" style="background:linear-gradient(120deg,#001d42,#0b2d63);">
    <div class="container text-center">
        <p class="text-uppercase small mb-2">Contact Us</p>
        <h1 class="display-6 fw-semibold mb-3" style="color: #ffffff">Get in Touch with Horizon</h1>
        <p class="mb-0 text-white-50">Learn anytime, anywhere with our online courses and earn fully recognized degrees from top universities worldwide.</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="la la-map-marker"></i>
                        </span>
                        <h3 class="h5 text-primary">Our Location</h3>
                        <p class="text-muted mb-0">
                            <a href="{{ $mapLink }}" class="text-muted text-decoration-none" target="_blank" rel="noopener">
                                {{ $address }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="la la-phone"></i>
                        </span>
                        <h3 class="h5 text-primary">Phone Number</h3>
                        <p class="text-muted mb-0">
                            <a href="{{ $telLink }}" class="text-muted text-decoration-none">
                                {{ $phoneDisplay }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="la la-envelope"></i>
                        </span>
                        <h3 class="h5 text-primary">Email Address</h3>
                        <p class="text-muted mb-0">
                            <a href="{{ $emailLink }}" class="text-muted text-decoration-none">
                                {{ $emailDisplay }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 text-primary mb-3">Send us a message</h2>
                        <p class="text-muted mb-4">{!! $info->description ?? 'Our advisors are ready to help you pick the perfect program and answer any admissions questions.' !!}</p>
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{route('contact.form')}}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" name="first_name" class="form-control" placeholder="First Name" value="{{ old('first_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="{{ old('last_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="+1 (833) 33-STUDY" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Message</label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Tell us how we can help">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn theme-btn w-100">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 text-primary mb-3">Visit or Schedule a Call</h2>
                        <p class="text-muted">Our team is available Monday to Friday from 9:00 AM to 9:00 PM (EST). We also host virtual consultations for students across the Middle East.</p>
                        <ul class="list-unstyled text-muted mb-4">
                            <li class="mb-2"><i class="la la-clock text-primary me-2"></i>Office Hours: Mon - Fri, 9:00 AM - 9:00 PM (EST)</li>
                            <li class="mb-2"><i class="la la-video text-primary me-2"></i>Virtual appointments available on Zoom</li>
                            <li class="mb-2"><i class="la la-globe text-primary me-2"></i>Serving UAE, Saudi Arabia, Qatar, Oman & beyond</li>
                        </ul>
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                            <iframe
                                src="https://www.google.com/maps?q={{ urlencode($info->address ?? 'Sterling, VA 20165') }}&output=embed"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

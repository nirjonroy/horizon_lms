@extends('frontend.app')

@php
    $SeoSettings = \App\Models\SeoSetting::forPage('appointment-for-consultation');
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
    <meta name="robots" content="index, follow">
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
    @if($favicon)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon }}">
    @endif
@endsection

@section('content')
<section class="py-5" style="background:#001d42;">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="display-5 fw-semibold" style="color:#ffffff">Book a Consultation</h1>
            <p class="lead mb-0">Secure a 20-minute call with our academic advisors and plan your next step.</p>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-comments fs-3"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted">Consultation Type</p>
                                <h5 class="mb-0">Online Video Call</h5>
                            </div>
                        </div>
                        <p class="text-muted mb-2"><i class="la la-clock me-2"></i> Duration: 20 minutes</p>
                        <p class="text-muted mb-4"><i class="la la-calendar me-2"></i> Today: {{ \Carbon\Carbon::now()->format('D, M d, Y') }}</p>
                        <hr>
                        <p class="text-muted mb-1">Need help now?</p>
                        <a href="mailto:support@thehorizonsunlimited.com" class="fw-semibold d-block mb-2">
                            support@thehorizonsunlimited.com
                        </a>
                        <a href="tel:+1631237884" class="fw-semibold d-block">(833) 33-STUDY</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="card-title fs-4 mb-4 text-primary">Select Date & Time</h3>
                        <form method="GET" action="{{ route('consultation.step2') }}">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="date" class="form-label fw-semibold">Choose a date</label>
                                    <input type="date" class="form-control" id="date" name="date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="time_zone" class="form-label fw-semibold">Select time zone</label>
                                    @php
                                        $selectedZone = old('time_zone', request('time_zone'));
                                    @endphp
                                    <select name="time_zone" id="time_zone" class="form-select" required>
                                        @include('frontend.partials.time_zone_options', ['selected' => $selectedZone])
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Available time slots</label>
                                    <p class="text-muted small mb-3">Choose a slot that works for you. All times are shown in the selected time zone.</p>
                                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2">
                                        @foreach($timeSlots as $slot)
                                            <div class="col">
                                                <button type="button" class="btn btn-outline-primary w-100 time-option" data-value="{{ $slot }}">
                                                    {{ $slot }}
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" id="selected-time" name="time" required>
                                </div>
                                @if ($errors->any())
                                    <div class="col-12">
                                        <div class="alert alert-danger mb-0">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12 pt-3">
                                    <button type="submit" class="btn theme-btn w-100">
                                        Continue <i class="la la-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.time-option');
        const hiddenInput = document.getElementById('selected-time');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                hiddenInput.value = button.dataset.value;
            });
        });
    });
</script>
@endsection

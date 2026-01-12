@extends('frontend.app')
@section('title', 'Horizon - Contact US')
@section('seos')
    @php
        $SeoSettings = DB::table('seo_settings')->where('id', 3)->first();
        // Decode the keywords JSON string into an array
        $keywordsArray = json_decode($SeoSettings->keywords, true);
    @endphp

    @php
    $siteInfo = DB::table('site_information')->first();
    @endphp

    <meta charset="UTF-8">

    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <meta name="title" content="{{$SeoSettings->seo_title}}">

    <meta name="description" content="{{$SeoSettings->seo_description}}">
    
    <!-- Populate the keywords meta tag -->
    <meta name="keywords" content="{{ isset($keywordsArray) ? implode(', ', $keywordsArray) : '' }}" /> 

    <meta property="og:title" content="{{$SeoSettings->seo_title}}">
    <meta property="og:description" content="{{$SeoSettings->seo_description}}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{$SeoSettings->seo_title}}">
    <meta property="og:image" content="{{asset($siteInfo->logo)}}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="628">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
@endsection

@section('content')
@php
    $selectedDate = request('date');
    $selectedTime = request('time');
    $selectedZone = request('time_zone') ?? 'UTC';
    $formattedDate = $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') : null;
@endphp
<section class="py-5" style="background:#001d42;">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="display-6 fw-semibold mb-2">Appointment for Consultation</h1>
            <p class="mb-0">Confirm your details to reserve the {{ $selectedTime }} slot.</p>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge bg-light text-primary border border-primary mb-3">Step 2 of 3</span>
                        <h3 class="h4 text-primary mb-4">Session Overview</h3>
                        <div class="d-flex align-items-center mb-3">
                            <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-video"></i>
                            </span>
                            <div>
                                <p class="mb-0 text-muted">Consultation Type</p>
                                <strong>Online Video Call</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-clock"></i>
                            </span>
                            <div>
                                <p class="mb-0 text-muted">Duration</p>
                                <strong>20 minutes</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-calendar"></i>
                            </span>
                            <div>
                                <p class="mb-0 text-muted">Date</p>
                                <strong>{{ $formattedDate ?? $selectedDate }}</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-3">
                                <i class="la la-globe"></i>
                            </span>
                            <div>
                                <p class="mb-0 text-muted">Time & Time Zone</p>
                                <strong>{{ $selectedTime }} ({{ $selectedZone }})</strong>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded-3">
                            <p class="text-muted small mb-1">Need to adjust the slot?</p>
                            <a href="{{ route('consultation.step1') }}" class="btn btn-outline-primary btn-sm">
                                Pick a different time
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="h4 text-primary mb-1">Almost there!</h3>
                        <p class="text-muted mb-4">Share a few details so our advisors can come prepared.</p>
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <script>
                                (function () {
                                    const messages = @json($errors->all());
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Please fix the highlighted fields',
                                            html: messages.map(m => `<div class="text-start">• ${m}</div>`).join(''),
                                            confirmButtonColor: '#ec5252'
                                        });
                                    }
                                })();
                            </script>
                        @endif
                        <form method="POST" action="{{ route('consultation.personal-info') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="date" value="{{ $selectedDate }}">
                            <input type="hidden" name="time" value="{{ $selectedTime }}">
                            <input type="hidden" name="time_zone" value="{{ $selectedZone }}">
                            <div class="col-md-6">
                                <label for="first-name" class="form-label fw-semibold">First Name *</label>
                                <input type="text" id="first-name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" required value="{{ old('first_name') }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last-name" class="form-label fw-semibold">Last Name *</label>
                                <input type="text" id="last-name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" required value="{{ old('last_name') }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone *</label>
                                <div class="input-group">
                                    <select
                                        name="country_code"
                                        id="country_code"
                                        class="form-select select2-dial @error('phone') is-invalid @enderror"
                                        required
                                    >
                                        @include('frontend.partials.country_dial_options')
                                    </select>
                                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone') }}">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="additional-info" class="form-label fw-semibold">Additional Information</label>
                                <textarea id="additional-info" name="additional_info" rows="4" class="form-control @error('additional_info') is-invalid @enderror" placeholder="Anything you'd like us to know before the call?">{{ old('additional_info') }}</textarea>
                                @error('additional_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="confirm" name="confirm" required>
                                    <label class="form-check-label text-muted" for="confirm">
                                        I agree to be contacted using the details provided.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn theme-btn w-100">
                                    Schedule Consultation <i class="la la-arrow-right ms-1"></i>
                                </button>
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
        const dialSelect = document.getElementById('country_code');
        const phoneInput = document.querySelector('input[name="phone"]');

        if (window.jQuery && $.fn.select2) {
            $('.select2-dial').select2({
                placeholder: 'Select code',
                width: '140px'
            });
        }

        if (dialSelect && phoneInput) {
            const defaultDial = "{{ old('country_code', '+880') }}";
            const applyDialCode = (code) => {
                const trimmed = phoneInput.value.trim();
                if (!trimmed || !trimmed.startsWith(code)) {
                    phoneInput.value = code + ' ';
                }
            };

            // Set initial
            if (window.jQuery && $.fn.select2) {
                $(dialSelect).val(defaultDial).trigger('change.select2');
            } else {
                dialSelect.value = defaultDial;
            }
            if (!phoneInput.value.trim()) {
                applyDialCode(defaultDial);
            }

            dialSelect.addEventListener('change', (e) => applyDialCode(e.target.value));
            if (window.jQuery && $.fn.select2) {
                $(dialSelect).on('change.select2', function () {
                    applyDialCode(this.value);
                });
            }
        }
    });
</script>
@endsection

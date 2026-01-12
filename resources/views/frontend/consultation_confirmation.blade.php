@extends('frontend.app')

@section('content')
@php
    $start = null;
    $end = null;
    $formattedDate = null;
    $formattedRange = null;

    try {
        if (!empty($booking->date) && !empty($booking->time)) {
            $start = \Carbon\Carbon::parse($booking->date . ' ' . $booking->time, config('app.timezone'));
            $end = (clone $start)->addMinutes(20);
            $formattedDate = $start->format('l, F j, Y');
            $formattedRange = $start->format('h:i A') . ' - ' . $end->format('h:i A');
        }
    } catch (\Exception $e) {
        $formattedDate = $booking->date ?? '';
        $formattedRange = $booking->time ?? '';
    }

    $timeZoneLabel = $booking->time_zone ?? 'UTC';
@endphp

<section class="py-5" style="background:#001d42;">
    <div class="container">
        <div class="text-center text-white">
            <h1 class="display-6 fw-semibold mb-2">Your Meeting Is Confirmed</h1>
            <p class="mb-0">We have locked in your 20 minute session. A confirmation email is on its way.</p>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-md-5 text-center">
                        <span class="badge bg-light text-success border border-success mb-3">Scheduled</span>
                        <h2 class="fw-bold text-primary mb-3">Your Meeting has been Scheduled</h2>
                        <p class="text-muted mb-4">
                            Thank you for your appointment request. We will reach out shortly with the conference link.
                            Need immediate assistance? Call us anytime at
                            <a href="tel:+12024597853" class="text-decoration-none fw-semibold">+1 (202) 459-7853</a>.
                        </p>

                        <div class="row g-3 text-start justify-content-center">
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted mb-1">Duration</p>
                                    <h5 class="mb-0">20 mins</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted mb-1">Date</p>
                                    <h5 class="mb-0">{{ $formattedDate ?? 'N/A' }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted mb-1">Time</p>
                                    <h5 class="mb-0">{{ $formattedRange ?? 'N/A' }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border rounded-3 p-3 bg-white text-start">
                            <p class="text-muted mb-1">Time Zone</p>
                            <div class="d-flex align-items-center">
                                <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary me-2">
                                    <i class="la la-globe"></i>
                                </span>
                                <strong>{{ $timeZoneLabel }}</strong>
                            </div>
                        </div>

                        <div class="alert alert-primary bg-primary bg-opacity-10 border-0 mt-4 text-start">
                            <div class="d-flex">
                                <i class="la la-info-circle fs-4 me-2 text-primary"></i>
                                <div>
                                    <strong class="d-block mb-1 text-primary">What happens next?</strong>
                                    <span class="text-muted">You will receive a reminder email with the video call link. Please join a few minutes early to ensure your audio/video are working.</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                            <a href="{{ route('home.index') }}" class="btn btn-outline-primary px-4">
                                Go to Homepage
                            </a>
                            <a href="{{ route('consultation.step1') }}" class="btn theme-btn px-4">
                                Book Another Consultation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

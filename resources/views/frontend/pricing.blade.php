@extends('frontend.app')
@section('title', 'Price & Plan | ' . config('app.name', 'Horizons'))

@section('content')
@php
    $heroImage = optional($heroPlan)->image ? asset($heroPlan->image) : asset('frontend/assets/images/slider-img2.jpg');
    $heroTitle = optional($heroPlan)->title ?? 'Study Online with Top Universities';
    $heroDescription = \Illuminate\Support\Str::limit(strip_tags(optional($heroPlan)->short_description), 140) ?: 'Transparent tuition, installment plans, and personalized guidance for every learning path.';
    $heroPrice = (float) (optional($heroPlan)->price ?? 0);
@endphp
<section class="py-5" style="background:linear-gradient(120deg,#5428f1,#6f7bff);">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 text-white">
                <p class="text-uppercase small mb-2">Featured Plan</p>
                <h1 class="display-5 fw-bold mb-3" style="color:white">{{ $heroTitle }}</h1>
                <p class="mb-4 lead">{{ $heroDescription }}</p>
                @if($heroPrice > 0)
                    <p class="fs-4 mb-4"><span class="fw-semibold">${{ number_format($heroPrice, 0) }}</span> access fee</p>
                @else
                    <p class="fs-4 mb-4"><span class="fw-semibold text-success">Free</span> access</p>
                @endif
                <div class="d-flex flex-wrap gap-3">
                    @if($heroPlan)
                        <a href="{{ route('course.show', $heroPlan->slug) }}" class="btn btn-light text-primary px-4">View Course</a>
                    @endif
                    <a href="{{ route('consultation.step1') }}" class="btn btn-outline-light px-4">Talk to Advisor</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow-lg">
                    <img src="{{ $heroImage }}" alt="Students" class="w-100 h-100 object-fit-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="icon-element icon-element-md bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="la la-graduation-cap"></i>
                        </span>
                        <h3 class="h5">Accredited Degrees</h3>
                        <p class="text-muted mb-0">Partner universities in the US, UK, Canada, and Europe with globally recognized diplomas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="icon-element icon-element-md bg-success bg-opacity-10 text-success mb-3">
                            <i class="la la-credit-card"></i>
                        </span>
                        <h3 class="h5">Flexible Payments</h3>
                        <p class="text-muted mb-0">Monthly, yearly, lifetime, and weekly access options built around your goals.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <span class="icon-element icon-element-md bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="la la-comments"></i>
                        </span>
                        <h3 class="h5">Dedicated Advisors</h3>
                        <p class="text-muted mb-0">Our team tailors a plan for your budget, admissions timeline, and visa requirements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase small text-muted mb-1">Recommended plans</p>
            <h2 class="fw-bold mb-2">Handpicked Pricing Options</h2>
            <p class="text-muted">Compare the most popular premium course plans by access type.</p>
        </div>
        <div class="row g-4">
            @forelse($featuredPlans as $plan)
                @php
                    $typeLabel = ucfirst($plan->type ?? 'Premium');
                    $price = (float) ($plan->price ?? 0);
                    $oldPrice = (float) ($plan->old_price ?? 0);
                    $hasDiscount = $oldPrice > $price && $price > 0;
                @endphp
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-lg rounded-4">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge bg-primary-subtle text-primary mb-2">{{ $typeLabel }} plan</span>
                            <h3 class="h5 fw-bold">{{ $plan->title }}</h3>
                            <p class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($plan->short_description), 80) }}</p>
                            <div class="my-3">
                                @if($price > 0)
                                    <span class="display-5 fw-bold text-primary">${{ number_format($price, 0) }}</span>
                                    <span class="text-muted d-block">Access fee</span>
                                    @if($hasDiscount)
                                        <span class="text-muted small"><s>${{ number_format($oldPrice, 0) }}</s></span>
                                    @endif
                                @else
                                    <span class="display-6 fw-bold text-success">Free</span>
                                @endif
                            </div>
                            <ul class="list-unstyled text-muted flex-grow-1">
                                <li class="mb-2"><i class="la la-check text-success me-2"></i>Duration: {{ $plan->duration ?? 'Flexible' }}</li>
                                <li class="mb-2"><i class="la la-check text-success me-2"></i>Effort: {{ $plan->effort ?? 'Self-paced' }}</li>
                                <li class="mb-2"><i class="la la-check text-success me-2"></i>Format: {{ ucfirst($plan->format ?? 'online') }}</li>
                            </ul>
                            <a href="{{ route('course.show', $plan->slug) }}" class="btn theme-btn w-100 mt-3">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No featured plans available right now.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">Full catalog</p>
                <h2 class="h5 fw-bold mb-0">Premium Course Plans by Access Type</h2>
            </div>
            <small class="text-muted">Prices shown in USD. Installment options discussed during consultation.</small>
        </div>
        <div class="table-responsive shadow-sm bg-white rounded-4">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Old Price</th>
                        <th>Duration</th>
                        <th>Format</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        @php
                            $price = (float) ($plan->price ?? 0);
                            $oldPrice = (float) ($plan->old_price ?? 0);
                        @endphp
                        <tr>
                            <td>
                                <p class="fw-semibold mb-0">{{ $plan->title }}</p>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($plan->short_description), 60) }}</small>
                            </td>
                            <td>{{ ucfirst($plan->type) }}</td>
                            <td>
                                @if($price > 0)
                                    ${{ number_format($price, 2) }}
                                @else
                                    <span class="text-success">Free</span>
                                @endif
                            </td>
                            <td>
                                @if($oldPrice > $price && $price > 0)
                                    <span class="text-muted"><s>${{ number_format($oldPrice, 2) }}</s></span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $plan->duration ?? 'Flexible' }}</td>
                            <td>{{ ucfirst($plan->format ?? 'online') }}</td>
                            <td class="text-end">
                                <a href="{{ route('course.show', $plan->slug) }}" class="btn btn-sm btn-outline-primary">
                                    View Course
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No pricing data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pt-4 d-flex justify-content-center">
            {{ $plans->links() }}
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <p class="text-uppercase text-muted small mb-2">FAQ</p>
                        <h2 class="h5 fw-bold mb-4">Frequently Asked Questions</h2>
                        <div class="accordion" id="pricingFaq">
                            @foreach($faqs as $index => $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faqHeading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $index }}">
                                            {{ $faq['question'] }}
                                        </button>
                                    </h2>
                                    <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#pricingFaq">
                                        <div class="accordion-body text-muted">
                                            {{ $faq['answer'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5 text-center">
                        <p class="text-uppercase text-muted small mb-2">Ready to start?</p>
                        <h2 class="h4 fw-bold mb-3">Talk to our advisors</h2>
                        <p class="text-muted mb-4">We'll build a tuition plan based on your target country, university, and budget.</p>
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('consultation.step1') }}" class="btn theme-btn">Book a Consultation</a>
                            <a href="{{ route('apply.now') }}" class="btn btn-outline-primary">Apply Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

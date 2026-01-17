@extends('frontend.app')

@section('title', 'Universities & Degrees')

@section('content')
@php
    $search = $search ?? '';
    $selectedDegree = $selectedDegree ?? null;
    $selectedUniversity = $selectedUniversity ?? null;
    $programCount = $programs->total();
    $hasFilters = $search !== '' || $selectedDegree || $selectedUniversity;
@endphp

<section class="breadcrumb-area section-padding img-bg-2" style="padding:50px">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h2 class="section__title text-white">Universities &amp; Degrees</h2>
                <p class="section__desc text-white-50 mb-0">
                    Browse programs by university and degree to find the right fit.
                </p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li>Universities</li>
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top:50px">
    <div class="container">
        <div class="filter-bar mb-4">
            <div class="filter-bar-inner d-flex flex-wrap align-items-center justify-content-between">
                <p class="fs-14 mb-2 mb-md-0">
                    We found <span class="text-black">{{ $programCount }}</span> programs available for you
                    @if($search !== '')
                        <span class="text-muted">matching "{{ $search }}"</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-5">
                @forelse($programs as $program)
                    @php
                        $university = $program->university;
                        $degreeName = optional($program->feesCategory)->name ?? 'Degree Program';
                        $programUrl = ($program->slug && $university && $university->slug)
                            ? route('university.program.show', ['slug' => $university->slug, 'program' => $program->slug])
                            : null;
                        $applyUrl = $program->link ?: ($program->slug ? route('apply.now', $program->slug) : route('apply.now'));
                        $tuition = $program->total_fee;
                        $discounted = $program->yearly;
                        $hasDiscount = $discounted && $tuition && (float) $discounted < (float) $tuition;
                        $displayPrice = $hasDiscount ? $discounted : $tuition;
                        $duration = $program->duration ?: 'Flexible duration';
                    @endphp
                    <div class="card card-item mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        @if($programUrl)
                                            <a href="{{ $programUrl }}">{{ $program->program }}</a>
                                        @else
                                            {{ $program->program }}
                                        @endif
                                    </h5>
                                    <p class="card-text text-muted mb-2">{{ $university->name ?? 'Partner University' }}</p>
                                    <span class="badge badge-info">{{ $degreeName }}</span>
                                </div>
                                <div class="text-start text-lg-end">
                                    <div class="text-black font-weight-bold">
                                        @if($displayPrice !== null)
                                            ${{ number_format((float) $displayPrice, 2) }}
                                        @else
                                            {{ __('Contact us') }}
                                        @endif
                                        @if($hasDiscount)
                                            <span class="before-price font-weight-medium">${{ number_format((float) $tuition, 2) }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">{{ $duration }}</small>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between mt-3 gap-2">
                                <div class="text-muted small">
                                    {{ $program->type ? ucfirst($program->type) : 'Online' }}
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($programUrl)
                                        <a href="{{ $programUrl }}" class="btn btn-sm btn-outline-secondary">View details</a>
                                    @endif
                                    <a href="{{ $applyUrl }}" class="btn btn-sm theme-btn" @if($program->link) target="_blank" rel="noopener" @endif>
                                        Apply now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info mb-4">
                        <strong>No programs found.</strong> Try adjusting your filters or search phrase.
                    </div>
                @endforelse

                <div class="text-center mt-4">
                    {{ $programs->onEachSide(1)->links('frontend.components.pagination') }}
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-item mb-4">
                    <div class="card-body">
                        <h3 class="widget-title border-bottom pb-3 mb-4">Search programs</h3>
                        <form method="GET" action="{{ route('universities.index') }}" class="form-box d-flex align-items-center">
                            <input type="text" class="form-control me-2" name="search" placeholder="Search programs" value="{{ $search }}">
                            <input type="hidden" name="degree" value="{{ $selectedDegree }}">
                            <input type="hidden" name="university" value="{{ $selectedUniversity }}">
                            <button class="btn theme-btn theme-btn-sm" type="submit">
                                <span class="la la-search"></span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card card-item mb-4">
                    <div class="card-body">
                        <h3 class="widget-title border-bottom pb-3 mb-4">Filter programs</h3>
                        <form method="GET" action="{{ route('universities.index') }}">
                            <div class="form-group mb-3">
                                <label class="form-label">University</label>
                                <select name="university" class="form-select">
                                    <option value="">All universities</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" {{ (string) $selectedUniversity === (string) $university->id ? 'selected' : '' }}>
                                            {{ $university->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Degree</label>
                                <select name="degree" class="form-select">
                                    <option value="">All degrees</option>
                                    @foreach($degrees as $degree)
                                        <option value="{{ $degree->id }}" {{ (string) $selectedDegree === (string) $degree->id ? 'selected' : '' }}>
                                            {{ $degree->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="search" value="{{ $search }}">
                            <button class="btn theme-btn w-100" type="submit">Apply filters</button>
                        </form>
                        @if($hasFilters)
                            <a href="{{ route('universities.index') }}" class="btn btn-sm btn-outline-warning w-100 mt-3">Clear filters</a>
                        @endif
                    </div>
                </div>

                <div class="card card-item">
                    <div class="card-body text-center">
                        <h3 class="widget-title mb-3">Need admission support?</h3>
                        <p class="text-muted mb-4">Talk with our advisors to compare universities and degrees.</p>
                        <a href="{{ route('consultation.step1') }}" class="theme-btn w-100">Book a consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

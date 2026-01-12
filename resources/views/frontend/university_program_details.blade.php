@extends('frontend.app')

@php
    $programTitle = $program->program ?: ($program->short_name ?: 'Program');
    $programBadge = $program->short_name ?: (optional($program->feesCategory)->name ?: 'Program');
    $degreeName = optional($program->feesCategory)->name ?: 'Degree Program';
    $duration = $program->duration ?: 'Flexible schedule';
    $programType = $program->type ?: 'Online';
    $totalFee = (float) ($program->total_fee ?? 0);
    $yearlyFee = (float) ($program->yearly ?? 0);
    $hasDiscount = $yearlyFee > 0 && $totalFee > 0;
    $displayFee = $hasDiscount ? $yearlyFee : $totalFee;
    $heroImage = $studies->slider1 ? asset($studies->slider1) : asset('frontend/assets/images/img8.jpg');
    $applyUrl = $program->link ?: route('apply.now', $program->slug ?? null);
    $applyAttrs = $program->link ? 'target="_blank" rel="noopener"' : '';
    $summarySource = $program->short_description ?: ($studies->short_description ?? '');
    $summary = \Illuminate\Support\Str::limit(strip_tags($summarySource), 220);
    $programOverview = $program->short_description ?: null;
    $programDetails = $program->long_description ?: null;
    $syllabusAvailable = ! empty($program->syllabus_pdf);
    $syllabusRoute = $syllabusAvailable
        ? route('university.program.syllabus', ['slug' => $studies->slug, 'program' => $program->slug])
        : null;
    $cleanValue = function ($value) {
        $decoded = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleaned = trim(strip_tags($decoded));
        if ($cleaned === '') {
            return null;
        }

        return \Illuminate\Support\Str::limit($cleaned, 140);
    };
    $faqs = collect([
        ['q' => $studies->faq_question_1 ?? null, 'a' => $studies->faq_answer_1 ?? null],
        ['q' => $studies->faq_question_2 ?? null, 'a' => $studies->faq_answer_2 ?? null],
        ['q' => $studies->faq_question_3 ?? null, 'a' => $studies->faq_answer_3 ?? null],
        ['q' => $studies->faq_question_4 ?? null, 'a' => $studies->faq_answer_4 ?? null],
        ['q' => $studies->faq_question_5 ?? null, 'a' => $studies->faq_answer_5 ?? null],
    ])->filter(fn ($item) => filled($item['q']) && filled($item['a']))->values();
    $highlights = collect([
        ['label' => 'Ranking', 'value' => $cleanValue($studies->rank ?? null)],
        ['label' => 'Awards', 'value' => $cleanValue($studies->award ?? null)],
        ['label' => 'Global Network', 'value' => $cleanValue($studies->global_network ?? null)],
        ['label' => 'Rated', 'value' => $cleanValue($studies->rated ?? null)],
    ])->filter(fn ($item) => filled($item['value']))->values();
@endphp

@section('title', $programTitle . ' | ' . $studies->name)
@section('seos')
    @php
        $SeoSettings = DB::table('seo_settings')->where('id', 1)->first();
        $siteInfo = DB::table('site_information')->first();
        $metaTitle = $programTitle . ' | ' . $studies->name;
        $metaDescription = $summary ?: ($SeoSettings->seo_description ?? '');
        $metaAuthor = data_get($studies, 'meta_author');
        $metaPublisher = data_get($studies, 'meta_publisher');
        $seoAuthor = $metaAuthor ?? ($SeoSettings->author ?? ($siteInfo->title ?? config('app.name')));
        $seoPublisher = $metaPublisher ?? ($SeoSettings->publisher ?? $seoAuthor);
    @endphp

    <meta charset="UTF-8">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="title" content="{{ $metaTitle }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="author" content="{{ $seoAuthor }}">
    <meta name="publisher" content="{{ $seoPublisher }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $studies->name }}">
    <meta property="og:image" content="{{ $heroImage }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="628">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
@endsection

@section('content')
<section class="text-white" style="background-image: linear-gradient(120deg, rgba(0, 18, 38, 0.92), rgba(230, 68, 60, 0.8)), url('{{ $heroImage }}'); background-size: cover; background-position: center;">
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a class="text-white-50" href="{{ route('home.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a class="text-white-50" href="{{ route('where.to.study', $studies->slug) }}">{{ $studies->name }}</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $programTitle }}</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-light text-danger mb-3">{{ $degreeName }}</span>
                <h1 class="display-5 fw-bold mb-3 text-white">{{ $programTitle }}</h1>
                <p class="text-white mb-4">
                    {{ $summary ?: 'Explore a focused program pathway from ' . $studies->name . ' designed for ambitious learners.' }}
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="la la-clock fs-4"></i>
                        <span>{{ $duration }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="la la-laptop fs-4"></i>
                        <span>{{ $programType }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="la la-graduation-cap fs-4"></i>
                        <span>{{ $programBadge }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4">
                        <p class="text-uppercase small text-muted mb-2">Tuition</p>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <span class="fs-2 fw-bold text-primary">${{ number_format($displayFee, 2) }}</span>
                            @if($hasDiscount)
                                <span class="text-muted text-decoration-line-through">${{ number_format($totalFee, 2) }}</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-4">
                            {{ $hasDiscount ? 'Discounted / yearly tuition shown.' : 'Total tuition shown.' }}
                        </p>
                        <a href="{{ $applyUrl }}" class="btn theme-btn w-100 mb-2" {!! $applyAttrs !!}>Apply Now</a>
                        @if($syllabusAvailable)
                            @auth
                                <a href="{{ $syllabusRoute }}" class="btn btn-outline-primary w-100 mb-2">Download syllabus (PDF)</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">Log in to download syllabus</a>
                            @endauth
                        @endif
                        <a href="{{ route('consultation.step1') }}" class="btn btn-outline-primary w-100">Talk to an advisor</a>
                    </div>
                </div>
                <div class="mt-3 text-white-50 small">
                    Updated {{ optional($program->updated_at ?? $program->created_at)->format('M d, Y') ?? 'recently' }}.
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 fw-bold text-primary mb-3">Program snapshot</h2>
                        <p class="text-muted mb-4">
                            A quick overview of the {{ $programTitle }} program offered by {{ $studies->name }}.
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="la la-graduation-cap fs-3 text-primary"></i>
                                        <div>
                                            <div class="text-muted small">Degree</div>
                                            <div class="fw-semibold">{{ $degreeName }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="la la-calendar fs-3 text-primary"></i>
                                        <div>
                                            <div class="text-muted small">Duration</div>
                                            <div class="fw-semibold">{{ $duration }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="la la-laptop fs-3 text-primary"></i>
                                        <div>
                                            <div class="text-muted small">Format</div>
                                            <div class="fw-semibold">{{ $programType }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="la la-money fs-3 text-primary"></i>
                                        <div>
                                            <div class="text-muted small">Tuition</div>
                                            <div class="fw-semibold">${{ number_format($displayFee, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($programOverview)
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="h4 fw-bold text-primary mb-3">Program overview</h2>
                            <div class="text-muted">
                                {!! $programOverview !!}
                            </div>
                        </div>
                    </div>
                @endif

                @if($programDetails)
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="h4 fw-bold text-primary mb-3">Program details</h2>
                            <div class="text-muted rich-text-content">
                                {!! $programDetails !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 fw-bold text-primary mb-3">About {{ $studies->name }}</h2>
                        <div class="text-muted">
                            {!! $studies->short_description ?: 'Learn more about this university and its commitment to delivering globally relevant education.' !!}
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 fw-bold text-primary mb-3">Tuition details</h2>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total tuition</td>
                                        <td>${{ number_format($totalFee, 2) }}</td>
                                        <td>Full program tuition.</td>
                                    </tr>
                                    <tr>
                                        <td>Discounted / yearly</td>
                                        <td>
                                            @if($hasDiscount)
                                                <span class="text-success fw-semibold">${{ number_format($yearlyFee, 2) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $hasDiscount ? 'Limited-time discount applied.' : 'No discount available.' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Duration</td>
                                        <td>{{ $duration }}</td>
                                        <td>Plan your schedule with advisor guidance.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="{{ $applyUrl }}" class="btn theme-btn" {!! $applyAttrs !!}>Apply Now</a>
                            <a href="{{ route('apply.now') }}" class="btn btn-outline-primary">Start application</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                @if($highlights->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold text-primary mb-3">University highlights</h3>
                            @foreach($highlights as $highlight)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="text-muted small">{{ $highlight['label'] }}</div>
                                    <div class="fw-semibold">{{ $highlight['value'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-bold text-primary mb-3">Need guidance?</h3>
                        <p class="text-muted mb-3">
                            Share your goals and we will match you with the right program and intake.
                        </p>
                        <a href="{{ route('consultation.step1') }}" class="btn theme-btn w-100 mb-2">Book consultation</a>
                        <a href="{{ route('contact.us') }}" class="btn btn-outline-primary w-100">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($faqs->isNotEmpty())
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">FAQs</h2>
                <p class="text-muted mb-0">Common questions about {{ $studies->name }} programs.</p>
            </div>
            <div class="accordion accordion-flush" id="programFaqs">
                @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading{{ $index }}">
                            <button class="accordion-button {{ $index ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index ? 'false' : 'true' }}" aria-controls="faqCollapse{{ $index }}">
                                {{ $faq['q'] }}
                            </button>
                        </h2>
                        <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index ? '' : 'show' }}" aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#programFaqs">
                            <div class="accordion-body">
                                {!! $faq['a'] !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if($relatedPrograms->isNotEmpty())
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-primary mb-1">Explore other programs</h2>
                    <p class="text-muted mb-0">More options from {{ $studies->name }}.</p>
                </div>
                <a href="{{ route('where.to.study', $studies->slug) }}" class="btn btn-outline-primary">Back to university</a>
            </div>
            <div class="row g-4">
                @foreach($relatedPrograms as $item)
                    @php
                        $itemTitle = $item->program ?: ($item->short_name ?: 'Program');
                        $itemFeeTotal = (float) ($item->total_fee ?? 0);
                        $itemFeeYearly = (float) ($item->yearly ?? 0);
                        $itemHasDiscount = $itemFeeYearly > 0 && $itemFeeTotal > 0;
                        $itemFeeDisplay = $itemHasDiscount ? $itemFeeYearly : $itemFeeTotal;
                        $itemUrl = $item->slug
                            ? route('university.program.show', ['slug' => $studies->slug, 'program' => $item->slug])
                            : route('apply.now', $item->slug ?? null);
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h3 class="h6 fw-semibold text-primary mb-2">
                                    <a href="{{ $itemUrl }}" class="text-decoration-none">{{ $itemTitle }}</a>
                                </h3>
                                <div class="text-muted small mb-3">{{ $item->duration ?: 'Flexible schedule' }}</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="fw-bold text-success">${{ number_format($itemFeeDisplay, 2) }}</span>
                                    @if($itemHasDiscount)
                                        <span class="text-muted text-decoration-line-through">${{ number_format($itemFeeTotal, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                                <a href="{{ $itemUrl }}" class="btn btn-outline-primary w-100">View details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection

@extends('frontend.app')

@php
    $siteInfo = \App\Models\siteInformation::first();
    $siteName = optional($siteInfo)->name ?: 'Horizons Unlimited';
    $contactPhone = optional($siteInfo)->mobile1 ?: optional($siteInfo)->mobile2;
    $contactEmail = optional($siteInfo)->email1 ?: optional($siteInfo)->email2;
    $contactAddress = optional($siteInfo)->address;
    $phoneHref = $contactPhone ? preg_replace('/[^+\d]/', '', $contactPhone) : null;
    $createdDate = optional($page->created_at)->format('F d, Y');
    $updatedDate = optional($page->updated_at)->format('F d, Y');
    $sidebarLinks = [
        ['label' => 'Premium Course Catalog', 'url' => route('premium-courses')],
        ['label' => 'Course Categories', 'url' => route('course.categories')],
        ['label' => 'Price & Plan', 'url' => route('price.plan')],
        ['label' => 'Blog & Guides', 'url' => route('all.blogs')],
        ['label' => 'Contact Us', 'url' => route('contact.us')],
    ];
@endphp

@section('title', $page->title . ' | Horizons Unlimited')

@section('seos')
    @php
        $description = \Illuminate\Support\Str::limit(strip_tags($page->content ?? ''), 160);
    @endphp
    <meta name="description" content="{{ $description }}">
@endsection

@section('content')
    <section class="breadcrumb-area section-padding img-bg-2">
        <div class="overlay"></div>
        <div class="container">
            <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
                <div class="section-heading mb-3 mb-lg-0">
                    <h1 class="section__title text-white mb-1">{{ $page->title }}</h1>
                    @if($updatedDate)
                        <p class="text-white-50 mb-0">Updated {{ $updatedDate }}</p>
                    @endif
                </div>
                <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                    <li><a href="{{ route('home.index') }}">Home</a></li>
                    <li>{{ $page->title }}</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="privacy-policy-area section--padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="card card-item">
                            <div class="card-body">
                                <h3 class="card-title fs-18 pb-2">Quick Search</h3>
                                <div class="divider"><span></span></div>
                                <form action="{{ route('search') }}" method="GET">
                                    <div class="form-group">
                                        <input class="form-control form--control ps-3" type="text" name="search" placeholder="Search Horizons Unlimited" />
                                        <p class="fs-13 text-muted mb-0">Press enter or click the button to search.</p>
                                    </div>
                                    <button type="submit" class="btn theme-btn w-100">
                                        <i class="la la-search me-2"></i>Search Now
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card card-item">
                            <div class="card-body">
                                <h3 class="card-title fs-18 pb-2">Helpful Links</h3>
                                <div class="divider"><span></span></div>
                                <ul class="generic-list-item">
                                    @foreach($sidebarLinks as $link)
                                        <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="card card-item">
                            <div class="card-body">
                                <h3 class="card-title fs-18 pb-2">Need Help?</h3>
                                <div class="divider"><span></span></div>
                                <ul class="generic-list-item">
                                    @if($contactPhone)
                                        <li class="d-flex align-items-center">
                                            <i class="la la-phone me-2 text-primary"></i>
                                            <a href="tel:{{ $phoneHref }}">{{ $contactPhone }}</a>
                                        </li>
                                    @endif
                                    @if($contactEmail)
                                        <li class="d-flex align-items-center">
                                            <i class="la la-envelope me-2 text-primary"></i>
                                            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                        </li>
                                    @endif
                                    @if($contactAddress)
                                        <li class="d-flex">
                                            <i class="la la-map-marker me-2 text-primary mt-1"></i>
                                            <span>{{ $contactAddress }}</span>
                                        </li>
                                    @endif
                                </ul>
                                <a href="{{ route('consultation.step1') }}" class="btn theme-btn w-100 mt-3">
                                    <i class="la la-comments me-2"></i>Book a Consultation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card card-item mb-4">
                        <div class="card-body">
                            <div class="policy-meta d-flex flex-wrap align-items-center justify-content-between">
                                @if($createdDate)
                                    <div>
                                        <span class="fs-13 text-uppercase text-muted">Effective Date</span>
                                        <h5 class="mb-0">{{ $createdDate }}</h5>
                                    </div>
                                @endif
                                @if($updatedDate)
                                    <div>
                                        <span class="fs-13 text-uppercase text-muted">Last Updated</span>
                                        <h5 class="mb-0">{{ $updatedDate }}</h5>
                                    </div>
                                @endif
                                <div>
                                    <span class="fs-13 text-uppercase text-muted">Applies to</span>
                                    <h5 class="mb-0">{{ $siteName }}</h5>
                                </div>
                            </div>
                            <p class="text-muted mt-3 mb-0">
                                Learn how {{ $siteName }} approaches this topic across our global learning community.
                            </p>
                        </div>
                    </div>
                    <div class="card card-item">
                        <div class="card-body page-content">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<style>
    .page-content h1,
    .page-content h2,
    .page-content h3 {
        color: #001D42;
        font-weight: 600;
        margin-top: 1.5rem;
    }

    .page-content p {
        margin-bottom: 1rem;
    }

    .page-content ul,
    .page-content ol {
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .page-content a {
        color: #001D42;
        text-decoration: underline;
    }

    .breadcrumb-area.section-padding {
        padding-top: 120px;
        padding-bottom: 80px;
    }

    .privacy-policy-area .card.card-item {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 18px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.03);
    }

    .privacy-policy-area .sidebar .card + .card {
        margin-top: 24px;
    }

    .privacy-policy-area .sidebar .card-title {
        font-size: 15px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .privacy-policy-area .policy-meta {
        gap: 1.5rem;
    }

    .privacy-policy-area .policy-meta h5 {
        font-size: 18px;
        color: #001D42;
    }

    .privacy-policy-area .policy-meta span {
        letter-spacing: 0.08em;
        font-weight: 600;
    }

    .privacy-policy-area .page-content {
        color: #4a4f62;
    }

    .privacy-policy-area .page-content blockquote {
        border-left: 4px solid #ff5d5d;
        margin: 1.5rem 0;
        padding-left: 1.25rem;
        font-style: italic;
        color: #222b45;
    }
</style>

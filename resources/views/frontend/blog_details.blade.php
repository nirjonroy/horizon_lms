@extends('frontend.app')
@php
    $defaultSiteName = config('app.name', 'Horizons Unlimited');
    $metaTitle = $blog->meta_title ?: $blog->title;
    $rawDescription = $blog->meta_description ?: strip_tags($blog->description ?? '');
    $metaDescription = \Illuminate\Support\Str::limit($rawDescription, 160, '');
    if (mb_strlen($rawDescription) > 160) {
        $metaDescription = rtrim($metaDescription) . '...';
    }
    $imagePath = $blog->meta_image ?: $blog->image;
    $metaImage = $imagePath ? asset($imagePath) : '';
    $metaAuthor = $blog->author ?: $defaultSiteName;
    $metaPublisher = $blog->publisher ?: $defaultSiteName;
    $metaCopyright = $blog->copyright ?: 'Copyright ' . $defaultSiteName;
    $metaSiteName = $blog->site_name ?: $defaultSiteName;
    $metaKeywords = $blog->keywords;
@endphp
@section('title', $metaTitle)
@section('seos')

    <meta charset="UTF-8">

    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <meta name="title" content="{{ $metaTitle }}">

    <meta name="description" content="{{ $metaDescription }}">
    
    @if($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}" />
    @endif

    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $metaSiteName }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="628">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
     <meta name="author" content="{{ $metaAuthor }}">
    <meta name="publisher" content="{{ $metaPublisher }}">
    <meta name="copyright" content="{{ $metaCopyright }}">
    <meta name="language" content="english">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $metaImage }}">
    

    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <meta name="twitter:site" content="{{ $metaSiteName }}">
@endsection
@section('content')
@php
    $coverImage = $blog->image ? asset($blog->image) : asset('frontend/assets/images/blog-bg.jpg');
    $publishDate = optional($blog->created_at)->format('M d, Y');
    $authorName = $blog->author ?? 'Horizons Team';
    $shareUrl = urlencode(url()->current());
@endphp

<section class="breadcrumb-area section-padding img-bg-2" style="background-image:linear-gradient(180deg, rgba(0,29,66,.85), rgba(0,29,66,.85)), url('{{ $coverImage }}'); padding:30px">
    <div class="overlay" style="opacity:0.2"></div>
    <div class="container">
        <div class="breadcrumb-content text-center text-white">
            <p class="fs-14 text-uppercase mb-2">Blog Details</p>
            <h1 class="section__title text-white mb-3">{{ $blog->title }}</h1>
            <p class="text-white-50 mb-0">
                <i class="la la-calendar me-1"></i>{{ $publishDate }}
                <span class="mx-2">•</span>
                <i class="la la-user me-1"></i>{{ $authorName }}
            </p>
        </div>
    </div>
</section>

<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="ratio ratio-16x9">
                        <img src="{{ $coverImage }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-wrap text-muted small mb-3 gap-3">
                            <span><i class="la la-calendar me-1"></i>{{ $publishDate }}</span>
                            <span><i class="la la-user me-1"></i>{{ $authorName }}</span>
                        </div>
                        <div class="content-wrapper text-muted">
                            {!! $blog->description !!}
                        </div>
                    </div>
                </article>

                <div class="card border-0 shadow-sm rounded-4 mt-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <h5 class="mb-0">Share this article</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="la la-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info"><i class="la la-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary"><i class="la la-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Recent Posts</h5>
                        <ul class="list-unstyled mb-0">
                            @foreach($recentBlogs as $recent)
                                <li class="d-flex mb-3 pb-3 border-bottom">
                                    <div class="flex-grow-1">
                                        <a href="{{ route('blog.details', $recent->slug) }}" class="fw-semibold text-dark d-block">
                                            {{ $recent->title }}
                                        </a>
                                        <small class="text-muted">{{ optional($recent->created_at)->format('M d, Y') }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-3">Need help choosing a program?</h5>
                        <p class="text-muted mb-3">Book a free consultation with our advisors to explore the best degree or pathway for you.</p>
                        <a href="{{ route('consultation.step1') }}" class="btn theme-btn w-100">Book Consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

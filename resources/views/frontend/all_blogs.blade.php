@extends('frontend.app')
@php
    $SeoSettings = \App\Models\SeoSetting::forPage('blog');
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
    $firstBlog = method_exists($blogs, 'first') ? $blogs->first() : null;
    $firstBlogImage = $firstBlog && isset($firstBlog->image) ? $firstBlog->image : null;
    $rawMetaImage = optional($SeoSettings)->image ?: $firstBlogImage ?: ($siteInfo->logo ?? null);
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
    <!--<meta property="og:image:width" content="1200">-->
    <!--<meta property="og:image:height" content="628">-->

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
    $defaultImage = asset('frontend/assets/images/blog-bg.jpg');
@endphp
<section class="breadcrumb-area section-padding img-bg-2" style="background-image:linear-gradient(180deg, rgba(0,29,66,.8), rgba(0,29,66,.8)), url('{{ $firstBlogImage ? asset($firstBlogImage) : $defaultImage }}'); padding:50px">
    <div class=""></div>
    <div class="container">
        <div class="breadcrumb-content text-center text-white">
            <p class="fs-14 text-uppercase mb-2 text-white" style="color:#ffffff !important">Insights & Stories</p>
            <h1 class="section__title text-white mb-3 text-white">Horizons of Insight</h1>
            <p class="section__desc text-white-50 text-white">Explore fresh perspectives on global education, online learning, and student success across the Middle East.</p>
        </div>
    </div>
</section>

<section class="course-area  bg-light" style="padding:50px">
    <div class="container">
        <div class="row g-4">
            @forelse($blogs as $blog)
                @php
                    $image = $blog->image ? asset($blog->image) : $defaultImage;
                    $excerpt = \Illuminate\Support\Str::limit(strip_tags($blog->description), 150);
                    $date = optional($blog->created_at)->format('M d, Y');
                @endphp
                <div class="col-lg-4 col-md-6">
                    <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="position-relative">
                            <a href="{{ route('blog.details', $blog->slug) }}">
                                <img src="{{ $image }}" alt="{{ $blog->title }}" class="card-img-top" style="height:230px; object-fit:cover;">
                            </a>
                            <span class="badge bg-primary position-absolute top-0 start-0 m-3">{{ $blog->category ?? 'Blog' }}</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span><i class="la la-calendar me-1"></i>{{ $date }}</span>
                                <span><i class="la la-user me-1"></i>{{ $blog->author ?? 'Horizons Team' }}</span>
                            </div>
                            <h3 class="h5 fw-semibold">
                                <a href="{{ route('blog.details', $blog->slug) }}" class="text-dark text-decoration-none">{{ $blog->title }}</a>
                            </h3>
                            <p class="text-muted flex-grow-1">{{ $excerpt }}</p>
                            <a href="{{ route('blog.details', $blog->slug) }}" class="btn theme-btn theme-btn-sm align-self-start">
                                Continue Reading <i class="la la-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No blog posts found.</div>
                </div>
            @endforelse
        </div>
        <div class="pt-4 d-flex justify-content-center">
            {{ $blogs->links() }}
        </div>
    </div>
</section>
@endsection

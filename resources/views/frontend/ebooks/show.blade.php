@extends('frontend.app')

@php
    $cover = $ebook->coverImageUrl();
    $metaImage = $ebook->metaImageUrl();
    $downloadLink = $ebook->downloadLink();
    $hasPaidAccess = $hasPaidAccess ?? false;
    $canPurchase = $ebook->canBePurchased();
    $previewUrl = $previewUrl ?? null;
    $previewPageLimit = $previewPageLimit ?? 5;
    $siteInfo = DB::table('site_information')->first();
    $normalizeUrl = function ($path) {
        if (! $path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };
    $seoTitle = $ebook->meta_title ?: $ebook->title;
    $seoDescription = $ebook->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($ebook->excerpt ?: $ebook->description ?: ''), 160, '');
    $siteName = $ebook->site_name ?: ($siteInfo->title ?? config('app.name'));
    $seoAuthor = $ebook->seo_author ?: ($ebook->author ?: ($siteInfo->title ?? config('app.name')));
    $publisher = $ebook->publisher ?: $siteName;
    $copyright = $ebook->copyright ?: $siteName;
    $keywordsContent = $ebook->keywords ?: collect([$ebook->title, $ebook->author, optional($ebook->category)->name, 'ebooks'])
        ->filter()
        ->implode(', ');
    $robots = $ebook->robots ?: 'index, follow';
    $favicon = $normalizeUrl($siteInfo->logo ?? null);
@endphp

@section('title', $seoTitle)
@section('seos')
    <meta name="robots" content="{{ $robots }}">
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $keywordsContent }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="book">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta name="author" content="{{ $seoAuthor }}">
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
    <meta name="twitter:image" content="{{ $metaImage }}">
    <meta name="twitter:site" content="{{ url()->current() }}">
@endsection

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">{{ $ebook->title }}</h1>
                <p class="section__desc text-white-50 mb-0">{{ $ebook->author ?? 'Unknown author' }}</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><a href="{{ route('ebooks.index') }}">E-Books</a></li>
                @if($ebook->category)
                    <li><a href="{{ route('ebooks.category.show', $ebook->category) }}">{{ $ebook->category->name }}</a></li>
                @endif
                <li>{{ $ebook->title }}</li>
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card card-item">
                    <div class="card-body text-center">
                        <img src="{{ $cover }}" alt="{{ $ebook->title }}" class="img-fluid rounded mb-4" style="max-height: 420px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                        @if($ebook->price !== null)
                            <div class="mb-4">
                                <div class="fs-3 font-weight-bold text-dark">
                                    ${{ number_format((float) $ebook->price, 2) }}
                                </div>
                                @if($ebook->old_price && (float) $ebook->old_price > (float) $ebook->price)
                                    <div class="before-price fs-18 mt-1">
                                        ${{ number_format((float) $ebook->old_price, 2) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="d-grid gap-2">
                            @if($hasPaidAccess)
                                @if($downloadLink || $ebook->ebook_file)
                                    <a href="{{ route('ebooks.download', $ebook->slug) }}" class="btn theme-btn w-100">
                                        Download E-Book
                                    </a>
                                    <p class="small text-success mb-0">Your payment has been confirmed. Download access is unlocked.</p>
                                @else
                                    <div class="alert alert-light border text-start mb-0">
                                        Download access is active, but the file is not available yet. Please contact support.
                                    </div>
                                @endif
                            @elseif($canPurchase)
                                @auth
                                    <form action="{{ route('ebooks.cart.add', $ebook->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn theme-btn w-100">Add to Cart</button>
                                    </form>
                                    <a href="{{ route('ebooks.cart.buy_now', $ebook->id) }}" class="btn btn-dark w-100">Buy Now</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn theme-btn w-100">Login to Purchase</a>
                                @endauth
                                <p class="small text-muted mb-0">Downloads are released after checkout is completed.</p>
                            @else
                                <div class="alert alert-light border text-start mb-0">
                                    This e-book is not currently available for online purchase.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card card-item mt-4">
                    <div class="card-body">
                        <h3 class="widget-title border-bottom pb-3 mb-4">Book Info</h3>
                        <ul class="generic-list-item">
                            <li><strong>Category:</strong> {{ optional($ebook->category)->name ?? 'Uncategorized' }}</li>
                            @if($ebook->isbn)
                                <li><strong>ISBN:</strong> {{ $ebook->isbn }}</li>
                            @endif
                            @if($ebook->language)
                                <li><strong>Language:</strong> {{ $ebook->language }}</li>
                            @endif
                            @if($ebook->pages)
                                <li><strong>Pages:</strong> {{ $ebook->pages }}</li>
                            @endif
                            @if($ebook->format)
                                <li><strong>Format:</strong> {{ $ebook->format }}</li>
                            @endif
                            @if($ebook->published_at)
                                <li><strong>Published:</strong> {{ $ebook->published_at->format('F j, Y') }}</li>
                            @endif
                            <li>
                                <strong>Price:</strong>
                                @if($ebook->price !== null)
                                    ${{ number_format((float) $ebook->price, 2) }}
                                @else
                                    {{ $hasPaidAccess ? 'Purchased' : 'Contact us' }}
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>

                @if(!$hasPaidAccess && (($relatedPlans ?? collect())->isNotEmpty() || ($relatedCollections ?? collect())->isNotEmpty()))
                    <div class="card card-item mt-4">
                        <div class="card-body">
                            <h3 class="widget-title border-bottom pb-3 mb-4">Unlock More Than One Book</h3>
                            @if(($relatedPlans ?? collect())->isNotEmpty())
                                <p class="text-muted mb-2">Access plans that include this title:</p>
                                <ul class="generic-list-item mb-3">
                                    @foreach($relatedPlans as $plan)
                                        <li>
                                            <a href="{{ route('ebook-plans.show', $plan->slug) }}">{{ $plan->name }}</a>
                                            <small class="text-muted d-block">{{ $plan->durationLabel() }} • {{ $plan->scopeLabel() }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if(($relatedCollections ?? collect())->isNotEmpty())
                                <p class="text-muted mb-2">Bundle collections containing this book:</p>
                                <ul class="generic-list-item mb-0">
                                    @foreach($relatedCollections as $collection)
                                        <li>
                                            <a href="{{ route('ebook-collections.show', $collection->slug) }}">{{ $collection->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="card card-item mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">About This Book</h2>
                        @if($ebook->excerpt)
                            <p class="lead text-muted">{{ $ebook->excerpt }}</p>
                        @endif

                        @if($ebook->description)
                            <div class="description-content">{!! $ebook->description !!}</div>
                        @else
                            <p class="text-muted mb-0">Description will be available soon.</p>
                        @endif
                    </div>
                </div>

                @if($previewUrl)
                    <div class="card card-item mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                <div class="mb-2 mb-md-0">
                                    <h3 class="mb-1">Book Preview</h3>
                                    <p class="text-muted mb-0">Read the first {{ $previewPageLimit }} pages before purchasing.</p>
                                </div>
                                <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                    Open Preview
                                </a>
                            </div>
                            <div class="border rounded overflow-hidden" style="height: 760px;">
                                <iframe
                                    src="{{ $previewUrl }}#toolbar=0&navpanes=0&scrollbar=1"
                                    title="Preview of {{ $ebook->title }}"
                                    style="width: 100%; height: 100%; border: 0;"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                @endif

                @if($relatedEbooks->isNotEmpty())
                    <div class="card card-item">
                        <div class="card-body">
                            <h3 class="widget-title border-bottom pb-3 mb-4">Related E-Books</h3>
                            <div class="row">
                                @foreach($relatedEbooks as $related)
                                    @php
                                        $relatedCover = $related->coverImageUrl();
                                    @endphp
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border">
                                            <img src="{{ $relatedCover }}" alt="{{ $related->title }}" class="card-img-top" style="height: 220px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                                            <div class="card-body">
                                                <span class="badge badge-light mb-2">{{ optional($related->category)->name ?? 'E-Book' }}</span>
                                                <h4 class="fs-18">
                                                    <a href="{{ route('ebooks.show', $related->slug) }}">{{ \Illuminate\Support\Str::limit($related->title, 60) }}</a>
                                                </h4>
                                                <p class="text-muted mb-3">{{ $related->author ?? 'Unknown author' }}</p>
                                                <a href="{{ route('ebooks.show', $related->slug) }}" class="btn btn-sm theme-btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$hasPaidAccess && (($relatedPlans ?? collect())->isNotEmpty() || ($relatedCollections ?? collect())->isNotEmpty()))
                    <div class="card card-item mt-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                                <div>
                                    <h3 class="widget-title mb-1">Access Options for This Book</h3>
                                    <p class="text-muted mb-0">Buy this title directly, unlock it with a plan, or get it inside a bundle.</p>
                                </div>
                            </div>
                            <div class="row">
                                @foreach(($relatedPlans ?? collect()) as $plan)
                                    <div class="col-md-6 mb-4">
                                        <div class="border rounded p-3 h-100">
                                            <span class="badge badge-info mb-2">{{ $plan->durationLabel() }}</span>
                                            <h4 class="fs-20"><a href="{{ route('ebook-plans.show', $plan->slug) }}">{{ $plan->name }}</a></h4>
                                            <p class="text-muted">{{ $plan->short_description ?: $plan->scopeLabel() }}</p>
                                            <strong class="d-block mb-3">${{ number_format((float) ($plan->price ?? 0), 2) }}</strong>
                                            <a href="{{ route('ebook-plans.show', $plan->slug) }}" class="btn btn-sm theme-btn">View Plan</a>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach(($relatedCollections ?? collect()) as $collection)
                                    <div class="col-md-6 mb-4">
                                        <div class="border rounded p-3 h-100">
                                            <span class="badge badge-light mb-2">{{ $collection->ebooks_count }} books</span>
                                            <h4 class="fs-20"><a href="{{ route('ebook-collections.show', $collection->slug) }}">{{ $collection->name }}</a></h4>
                                            <p class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($collection->excerpt ?: $collection->description), 100) }}</p>
                                            <strong class="d-block mb-3">${{ number_format((float) ($collection->price ?? 0), 2) }}</strong>
                                            <a href="{{ route('ebook-collections.show', $collection->slug) }}" class="btn btn-sm theme-btn">View Bundle</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

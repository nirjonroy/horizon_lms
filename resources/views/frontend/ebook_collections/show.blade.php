@extends('frontend.app')

@section('title', $collection->name)

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">{{ $collection->name }}</h1>
                <p class="section__desc text-white-50 mb-0">{{ $collection->excerpt ?: $collection->accessLabel() }}</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><a href="{{ route('ebook-collections.index') }}">Bundle Collections</a></li>
                <li>{{ $collection->name }}</li>
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
                        <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" class="img-fluid rounded mb-4" style="max-height: 260px; object-fit: cover;">
                        <div class="mb-3">
                            <strong class="fs-30">${{ number_format((float) ($collection->price ?? 0), 2) }}</strong>
                            @if($collection->old_price && (float) $collection->old_price > (float) $collection->price)
                                <div class="before-price fs-18 mt-1">${{ number_format((float) $collection->old_price, 2) }}</div>
                            @endif
                        </div>
                        <p class="text-muted">{{ $collection->accessLabel() }}</p>
                        <div class="d-grid gap-2">
                            @if($hasAccess)
                                <a href="{{ route('ebooks.index') }}" class="btn theme-btn w-100">Browse Included E-Books</a>
                                <p class="small text-success mb-0">This bundle is already unlocked on your account.</p>
                            @elseif($collection->canBePurchased())
                                @auth
                                    <form action="{{ route('ebook-collections.cart.add', $collection->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn theme-btn w-100">Add Bundle to Cart</button>
                                    </form>
                                    <a href="{{ route('ebook-collections.cart.buy_now', $collection->id) }}" class="btn btn-dark w-100">Buy Now</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn theme-btn w-100">Login to Purchase</a>
                                @endauth
                            @else
                                <div class="alert alert-light border text-start mb-0">This bundle is not currently available.</div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($collection->accessPlans->isNotEmpty())
                    <div class="card card-item mt-4">
                        <div class="card-body">
                            <h3 class="widget-title border-bottom pb-3 mb-4">Available Through Plans</h3>
                            <ul class="generic-list-item">
                                @foreach($collection->accessPlans as $plan)
                                    <li>
                                        <a href="{{ route('ebook-plans.show', $plan->slug) }}">{{ $plan->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="card card-item mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">About This Bundle</h2>
                        @if($collection->description)
                            <div class="description-content">{!! $collection->description !!}</div>
                        @else
                            <p class="text-muted mb-0">{{ $collection->excerpt ?: 'Bundle details will be available soon.' }}</p>
                        @endif
                    </div>
                </div>

                <div class="card card-item">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                            <div>
                                <h3 class="widget-title mb-1">Included Books</h3>
                                <p class="text-muted mb-0">{{ $collection->ebooks->count() }} books in this bundle</p>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($collection->ebooks as $ebook)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border">
                                        <img src="{{ $ebook->coverImageUrl() }}" alt="{{ $ebook->title }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h4 class="fs-18"><a href="{{ route('ebooks.show', $ebook->slug) }}">{{ $ebook->title }}</a></h4>
                                            <p class="text-muted mb-3">{{ $ebook->author ?? 'Unknown author' }}</p>
                                            <a href="{{ route('ebooks.show', $ebook->slug) }}" class="btn btn-sm theme-btn mt-auto">View Book</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('frontend.app')

@section('title', $plan->name)

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">{{ $plan->name }}</h1>
                <p class="section__desc text-white-50 mb-0">{{ $plan->tagline ?: $plan->scopeLabel() }}</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><a href="{{ route('ebook-plans.index') }}">E-Book Plans</a></li>
                <li>{{ $plan->name }}</li>
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
                        <img src="{{ $plan->imageUrl() }}" alt="{{ $plan->name }}" class="img-fluid rounded mb-4" style="max-height: 260px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                        <span class="badge badge-info mb-3">{{ $plan->durationLabel() }}</span>
                        <h2 class="fs-30 mb-2">${{ number_format((float) ($plan->price ?? 0), 2) }}</h2>
                        @if($plan->old_price && (float) $plan->old_price > (float) $plan->price)
                            <div class="before-price fs-18 mb-3">${{ number_format((float) $plan->old_price, 2) }}</div>
                        @endif
                        <div class="d-grid gap-2">
                            @if($hasAccess)
                                <a href="{{ route('ebooks.index') }}" class="btn theme-btn w-100">Browse Included E-Books</a>
                                <p class="small text-success mb-0">Your account already has this access.</p>
                            @elseif($plan->canBePurchased())
                                @auth
                                    <form action="{{ route('ebook-plans.cart.add', $plan->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn theme-btn w-100">Add Plan to Cart</button>
                                    </form>
                                    <a href="{{ route('ebook-plans.cart.buy_now', $plan->id) }}" class="btn btn-dark w-100">Buy Now</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn theme-btn w-100">Login to Purchase</a>
                                @endauth
                            @else
                                <div class="alert alert-light border text-start mb-0">This plan is not currently available.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-item mb-4">
                    <div class="card-body">
                        <h2 class="mb-3">Plan Overview</h2>
                        <p class="lead text-muted">{{ $plan->short_description ?: $plan->scopeLabel() }}</p>
                        <ul class="generic-list-item generic-list-item-flush mb-4">
                            <li><strong>Access scope:</strong> {{ $plan->scopeLabel() }}</li>
                            <li><strong>Billing cycle:</strong> {{ $plan->billingCycleLabel() }}</li>
                            <li><strong>Duration:</strong> {{ $plan->durationLabel() }}</li>
                        </ul>
                        @if($plan->description)
                            <div class="description-content">{!! $plan->description !!}</div>
                        @endif
                    </div>
                </div>

                @if($plan->collection)
                    <div class="card card-item">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                                <div>
                                    <h3 class="widget-title mb-1">Included Bundle Collection</h3>
                                    <p class="text-muted mb-0">{{ $plan->collection->name }}</p>
                                </div>
                                <a href="{{ route('ebook-collections.show', $plan->collection->slug) }}" class="btn btn-outline-secondary btn-sm">Open Bundle</a>
                            </div>
                            @if($plan->collection->ebooks->isNotEmpty())
                                <div class="row">
                                    @foreach($plan->collection->ebooks->take(6) as $ebook)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <img src="{{ $ebook->coverImageUrl() }}" alt="{{ $ebook->title }}" class="card-img-top" style="height: 220px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('frontend/assets/images/books-to-go-placeholder.svg') }}';">
                                                <div class="card-body">
                                                    <h4 class="fs-18"><a href="{{ route('ebooks.show', $ebook->slug) }}">{{ $ebook->title }}</a></h4>
                                                    <p class="text-muted mb-0">{{ $ebook->author ?? 'Unknown author' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">This plan unlocks a direct-download bundle package.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection



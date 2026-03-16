@extends('frontend.app')

@section('title', 'E-Book Bundle Collections')

@section('content')
<section class="breadcrumb-area section-padding img-bg-2" style="padding: 50px 0;">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading mb-3 mb-lg-0">
                <h1 class="section__title text-white">Bundle Collections</h1>
                <p class="section__desc text-white-50 mb-0">Buy curated e-book sets for a single price and unlock the full collection instantly after checkout.</p>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li>Bundle Collections</li>
            </ul>
        </div>
    </div>
</section>

<section class="course-area section--padding" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            @forelse($collections as $collection)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-item h-100">
                        <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" class="card-img-top" style="height: 260px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <span class="badge badge-light mb-2 align-self-start">{{ $collection->ebooks_count }} books</span>
                            <h3 class="card-title fs-22">
                                <a href="{{ route('ebook-collections.show', $collection->slug) }}">{{ $collection->name }}</a>
                            </h3>
                            <p class="text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit(strip_tags($collection->excerpt ?: $collection->description ?: ''), 110) }}</p>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <strong>${{ number_format((float) ($collection->price ?? 0), 2) }}</strong>
                                <a href="{{ route('ebook-collections.show', $collection->slug) }}" class="btn btn-sm theme-btn">View Bundle</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No bundle collections are available right now.</div>
                </div>
            @endforelse
        </div>

        <div class="pt-3 d-flex justify-content-center">
            {{ $collections->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>
@endsection

@extends('frontend.app')
@php
    $defaultSiteName = config('app.name', 'Horizons Unlimited');
    $metaTitle = $course->meta_title ?: $course->title;
    $rawDescription = $course->meta_description ?: strip_tags($course->short_description ?? '');
    $metaDescription = \Illuminate\Support\Str::limit($rawDescription, 160, '');
    if (mb_strlen($rawDescription) > 160) {
        $metaDescription = rtrim($metaDescription) . '...';
    }
    $imagePath = $course->meta_image ?: $course->image;
    $metaImage = $imagePath ? asset($imagePath) : '';
    $metaAuthor = $course->author ?: $defaultSiteName;
    $metaPublisher = $course->publisher ?: $defaultSiteName;
    $metaCopyright = $course->copyright ?: 'Copyright ' . $defaultSiteName;
    $metaSiteName = $course->site_name ?: $defaultSiteName;
    $metaKeywords = $course->keywords;
    $defaultPreviewImage = asset('frontend/assets/images/img8.jpg');
    $normalizeImagePath = function ($path) use ($defaultPreviewImage) {
        if (! $path) {
            return $defaultPreviewImage;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
    };
    $heroSummary = \Illuminate\Support\Str::limit(strip_tags($course->short_description ?? ''), 220);
    $normalizedCourseImage = $normalizeImagePath($course->image ?? null);
    $pricing = \App\Services\CampaignService::pricingForCourse($course);
    $reviewTotal = $reviewCount ?? 0;
    $averageRatingValue = $averageRating ?? 0;
    $averageRatingStars = (int) round($averageRatingValue);
    $canReview = $hasPurchased ?? false;
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
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
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
    @if($metaImage)
        <!--<link rel="icon" type="image/png" sizes="32x32" href="{{ $metaImage }}">-->
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:site" content="{{ $metaSiteName }}">
@endsection
@section('content')
<style>
    /*ul{*/
    /*    list-style-type : disc;*/
    /*}*/
    
    
</style>
<section class="breadcrumb-area section-padding img-bg-2" style="background-image: linear-gradient(180deg, rgba(7, 16, 45, 0.85), rgba(7, 16, 45, 0.85)), url('{{ $normalizedCourseImage }}'); padding: 5px">
    <div class="overlay" style="opacity:0.2"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="breadcrumb-content pt-80px pb-4">
                    <div class="section-heading">
                        <h1 class="section__title text-white">{{ $course->title }}</h1>
                        @if($heroSummary)
                            <p class="section__desc text-white-50 pt-2">{{ $heroSummary }}</p>
                        @endif
                    </div>
                    @php
                        $lastUpdated = optional($course->updated_at ?? $course->created_at)->format('M d, Y');
                    @endphp
                    <div class="d-flex flex-wrap align-items-center text-white pt-3">
                        <p class="pe-3 me-3 border-end mb-2 mb-lg-0" style="border-color: rgba(255,255,255,0.3)!important;">
                            <i class="la la-user me-1"></i>{{ $course->instructor ?? 'Horizons Faculty' }}
                        </p>
                        <p class="pe-3 me-3 border-end mb-2 mb-lg-0" style="border-color: rgba(255,255,255,0.3)!important;">
                            <i class="la la-clock me-1"></i>{{ $course->duration ?? 'Self-paced' }}
                        </p>
                        <p class="mb-0">
                            <i class="la la-calendar me-1"></i>Last updated {{ $lastUpdated ?? 'Recently' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="course-details-area pb-20px">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 pb-5">
                <div class="course-details-content-wrap pt-90px">
                    <div class="course-overview-card bg-gray p-4 rounded mb-4">
                        <h2 class="fs-24 font-weight-semi-bold pb-3">Course overview</h2>
                        <div class="fs-15 text-gray">
                            {!! $course->short_description ?? 'This premium experience is handcrafted by Horizons to give you an immediate advantage in your learning journey.' !!}
                        </div>
                    </div>
                    <div class="course-overview-card mb-4">
                        <h2 class="fs-24 font-weight-semi-bold pb-3">What you&apos;ll learn</h2>
                        <div class="fs-15 text-gray rich-text-content">
                            {!! $course->long_description !!}
                        </div>
                    </div>
                    <div class="course-overview-card border border-gray p-4 rounded mb-4">
                        <h2 class="fs-20 font-weight-semi-bold pb-3">Course highlights</h2>
                        <ul class="generic-list-item generic-list-item-bullet fs-15 mb-0">
                            <li><strong>Duration:</strong> {{ $course->duration ?? 'Self-paced' }}</li>
                            <li><strong>Effort:</strong> {{ $course->effort ?? 'Flexible schedule' }}</li>
                            <li><strong>Format:</strong> {{ $course->format ?? 'Online program' }}</li>
                            <li><strong>Assessments:</strong> {{ $course->questions ?? 'Curated assignments & quizzes' }}</li>
                            <li><strong>Category:</strong> {{ optional($course->category)->name ?? 'Premium Course' }}</li>
                        </ul>
                    </div>
                    <div class="course-overview-card border border-gray p-4 rounded mb-4" id="reviews">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="fs-24 font-weight-semi-bold mb-1">Student reviews</h2>
                                <p class="text-muted mb-0">See what recent learners shared about this course.</p>
                            </div>
                            <div class="d-flex align-items-center mt-3 mt-lg-0">
                                <div class="review-stars me-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="la {{ $i <= $averageRatingStars ? 'la-star' : 'la-star-o' }}"></span>
                                    @endfor
                                </div>
                                <span class="rating-number">{{ number_format($averageRatingValue, 1) }}</span>
                                <span class="text-muted ms-2">({{ number_format($reviewTotal) }} reviews)</span>
                            </div>
                        </div>
                        @if($reviews->isEmpty())
                            <p class="text-muted mb-4">No reviews yet. Be the first to share your experience.</p>
                        @else
                            <div class="mb-4">
                                @foreach($reviews as $review)
                                    @php
                                        $reviewRating = max(0, min(5, (int) $review->rating));
                                        $reviewDate = optional($review->created_at)->format('M d, Y');
                                        $reviewer = $review->user->name ?? 'Student';
                                    @endphp
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <div>
                                                <h3 class="fs-16 font-weight-semi-bold mb-1">{{ $reviewer }}</h3>
                                                <div class="review-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span class="la {{ $i <= $reviewRating ? 'la-star' : 'la-star-o' }}"></span>
                                                    @endfor
                                                </div>
                                            </div>
                                            @if($reviewDate)
                                                <span class="text-muted fs-14">{{ $reviewDate }}</span>
                                            @endif
                                        </div>
                                        <p class="text-gray mb-0 mt-2">{{ $review->review }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @auth
                            @if($canReview)
                                <div class="pt-2">
                                    <h3 class="fs-20 font-weight-semi-bold mb-3">
                                        {{ $userReview ? 'Update your review' : 'Write a review' }}
                                    </h3>
                                    <form action="{{ route('course.reviews.store', $course->slug) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="rating" class="form-label">Rating</label>
                                                <select name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror" required>
                                                    <option value="" disabled {{ old('rating', $userReview->rating ?? null) ? '' : 'selected' }}>Select rating</option>
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}" {{ (int) old('rating', $userReview->rating ?? 0) === $i ? 'selected' : '' }}>
                                                            {{ $i }} star{{ $i > 1 ? 's' : '' }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                @error('rating')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <label for="review" class="form-label">Your review</label>
                                                <textarea name="review" id="review" rows="4" class="form-control @error('review') is-invalid @enderror" placeholder="Share what you liked and who this course is best for." required>{{ old('review', $userReview->review ?? '') }}</textarea>
                                                @error('review')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <button type="submit" class="btn theme-btn">
                                            {{ $userReview ? 'Update review' : 'Submit review' }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="bg-gray p-3 rounded">
                                    <p class="mb-2">Only enrolled students can review this course.</p>
                                    @if($course->type !== 'free')
                                        <a href="{{ route('cart.buy_now', $course->id) }}" class="btn theme-btn theme-btn-sm">Purchase to review</a>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="bg-gray p-3 rounded">
                                <p class="mb-2">Want to share your experience?</p>
                                <a href="{{ route('login') }}" class="btn theme-btn theme-btn-sm">Log in to write a review</a>
                            </div>
                        @endauth
                    </div>
                    @if(isset($mostPopularCourses) && $mostPopularCourses->count())
                        <div class="course-overview-card pt-4">
                            <h2 class="fs-24 font-weight-semi-bold pb-3">Most popular courses</h2>
                            <div class="row">
                                @foreach($mostPopularCourses as $popCourse)
                                    @php
                                        $popImage = $normalizeImagePath($popCourse->image ?? null);
                                        $popPricing = \App\Services\CampaignService::pricingForCourse($popCourse);
                                    @endphp
                                    <div class="col-md-6 mb-4">
                                        <div class="card card-item card-preview h-100">
                                            <div class="card-image">
                                                <a href="{{ route('course.show', $popCourse->slug) }}" class="d-block">
                                                    <img class="card-img-top" src="{{ $popImage }}" alt="{{ $popCourse->title }}">
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <h2 class="card-title">
                                                    <a href="{{ route('course.show', $popCourse->slug) }}">{{ $popCourse->title }}</a>
                                                </h2>
                                                <p class="card-text">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($popCourse->short_description), 80) }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center pt-2">
                                                    <div class="card-price text-black font-weight-bold mb-0">
                                                        {{ is_null($popPricing->sale_price) ? 'Free' : number_format((float) $popPricing->sale_price, 2) }}
                                                        @if($popPricing->strike_price)
                                                            <span class="before-price ms-1">${{ number_format((float) $popPricing->strike_price, 2) }}</span>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('course.show', $popCourse->slug) }}" class="btn btn-sm theme-btn">
                                                        Explore
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="pt-4">
                        <a href="{{ route('premium-courses') }}" class="btn theme-btn theme-btn-sm theme-btn-white">
                            <i class="la la-arrow-left me-1"></i>Back to all courses
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-md-none position-fixed start-0 end-0 bottom-0 bg-white border-top shadow-lg p-3 zindex-fixed">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fs-18 fw-bold text-black">
                                {{ is_null($pricing->sale_price) ? 'Free' : '$'.number_format((float) $pricing->sale_price, 2) }}
                            </span>
                            @if(!empty($pricing->strike_price))
                                <span class="before-price ms-2">{{ number_format((float) $pricing->strike_price, 2) }}</span>
                            @endif
                        </div>
                        <span class="badge bg-3 text-white text-uppercase">{{ $course->type ?? 'Premium' }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        @auth
                            @if($course->type == 'free')
                                <a href="{{ $course->link }}" class="btn theme-btn flex-fill" target="_blank">
                                    <i class="la la-download me-1"></i>Download
                                </a>
                            @else
                                <form action="{{ route('cart.add', $course->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $course->title }}">
                                    <input type="hidden" name="price" value="{{ $pricing->sale_price ?? 0 }}">
                                    <input type="hidden" name="image" value="{{ $course->image }}">
                                    <button type="submit" class="btn theme-btn w-100">
                                        <i class="la la-shopping-cart me-1"></i>Add to cart
                                    </button>
                                </form>
                                <a href="{{ route('cart.buy_now', $course->id) }}" class="btn btn-dark flex-fill">
                                    Buy now
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn theme-btn flex-fill">
                                <i class="la la-lock me-1"></i>Login to continue
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-dark flex-fill">
                                Buy now
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="course-sidebar pt-90px">
                    <div class="card card-item rounded shadow-sm mb-4">
                        <div class="card-image">
                            <img src="{{ $normalizedCourseImage }}" alt="{{ $course->title }}" class="card-img-top">
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-baseline justify-content-between pb-3 border-bottom border-bottom-gray">
                                <div>
                                    <span class="fs-28 font-weight-bold text-black">
                                        {{ is_null($pricing->sale_price) ? 'Free' : number_format((float) $pricing->sale_price, 2) }}
                                    </span>
                                    @if(!empty($pricing->strike_price))
                                        <span class="before-price ms-2">
                                            {{ number_format((float) $pricing->strike_price, 2) }}
                                        </span>
                                    @endif
                                    @if($pricing->badge)
                                        <span class="badge badge-warning ms-2">{{ $pricing->badge }}</span>
                                    @endif
                                </div>
                                <span class="badge bg-3 text-white text-uppercase">{{ $course->type ?? 'Premium' }}</span>
                            </div>
                            <div class="pt-4">
                                @auth
                                    @if($course->type == 'free')
                                        <a href="{{ $course->link }}" class="btn theme-btn w-100 mb-2" target="_blank">
                                            <i class="la la-download me-1"></i>Download course
                                        </a>
                                    @else
                                        <form action="{{ route('cart.add', $course->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="name" value="{{ $course->title }}">
                                            <input type="hidden" name="price" value="{{ $pricing->sale_price ?? 0 }}">
                                            <input type="hidden" name="image" value="{{ $course->image }}">
                                            <button type="submit" class="btn theme-btn w-100 mb-2">
                                                <i class="la la-shopping-cart me-1"></i>Add to cart
                                            </button>
                                        </form>
                                        <a href="{{ route('cart.buy_now', $course->id) }}" class="btn btn-dark w-100 mb-2">
                                            Buy now
                                        </a>
                                    @endif
                                @else
                                    @if($course->type == 'free')
                                        <a href="{{ route('login') }}" class="btn theme-btn w-100 mb-2">
                                            <i class="la la-unlock-alt me-1"></i>Login to get link
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn theme-btn w-100 mb-2">
                                            <i class="la la-lock me-1"></i>Login to add to cart
                                        </a>
                                        <a href="{{ route('login') }}" class="btn btn-dark w-100 mb-2">
                                            Buy now
                                        </a>
                                        
                                    @endif
                                @endauth
                            </div>
                            <ul class="generic-list-item fs-15 pt-3 border-top border-top-gray mb-0">
                                <li><i class="la la-clock me-1 text-black"></i>Duration: {{ $course->duration ?? 'Self-paced' }}</li>
                                <li><i class="la la-line-chart me-1 text-black"></i>Effort: {{ $course->effort ?? 'Flexible' }}</li>
                                <li><i class="la la-question-circle me-1 text-black"></i>Questions: {{ $course->questions ?? 'Included' }}</li>
                                <li><i class="la la-laptop me-1 text-black"></i>Format: {{ $course->format ?? 'Online' }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card card-item p-4">
                        <h5 class="font-weight-semi-bold pb-2">Need help?</h5>
                        <p class="fs-15 mb-2">
                            Talk to our enrollment team at
                            <a href="mailto:info@thehorizonsunlimited.com">info@thehorizonsunlimited.com</a>
                        </p>
                        <a href="{{ route('contact.us') }}" class="btn theme-btn theme-btn-sm">
                            Contact us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

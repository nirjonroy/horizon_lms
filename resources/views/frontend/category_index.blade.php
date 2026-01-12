@extends('frontend.app')
@php
    use Illuminate\Support\Str;
    $search = trim((string) ($search ?? ''));
    $heroImage = asset('frontend/assets/images/slider-img3.jpg');
    $fallbackImages = [
        asset('frontend/assets/images/img8.jpg'),
        asset('frontend/assets/images/img9.jpg'),
        asset('frontend/assets/images/img10.jpg'),
        asset('frontend/assets/images/img11.jpg'),
    ];
@endphp
@section('title', 'Course Categories | ' . config('app.name', 'Horizons'))
@section('content')
    <section class="py-5" style="background: linear-gradient(135deg, #001d42, rgba(0, 29, 66, .7)), url('{{ $heroImage }}') center/cover no-repeat;">
        <div class="container text-white text-center">
            <p class="text-uppercase small mb-2">Browse Categories</p>
            <h1 class="display-5 fw-semibold mb-3">Find the Right Learning Path</h1>
            <p class="text-white-50 mb-0">Explore every premium course category available on Horizons and jump into the topics that matter most to you.</p>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-muted">Search categories</label>
                            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Try Business, Engineering, Design..." />
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button class="btn theme-btn flex-grow-1" type="submit">Search</button>
                            @if($search !== '')
                                <a href="{{ route('course.categories') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                @forelse($categories as $index => $category)
                    @php
                        $image = $category->image
                            ? (filter_var($category->image, FILTER_VALIDATE_URL) ? $category->image : asset($category->image))
                            : $fallbackImages[$index % count($fallbackImages)];
                        $description = Str::limit(strip_tags($category->description), 130) ?: 'Hand-picked premium courses designed to accelerate your learning journey.';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="ratio ratio-16x9 rounded-top-4 overflow-hidden">
                                <img src="{{ $image }}" alt="{{ $category->name }}" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-primary-subtle text-primary w-auto mb-2">Category</span>
                                <h3 class="h5 text-primary">{{ $category->name }}</h3>
                                <p class="text-muted flex-grow-1">{{ $description }}</p>
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                                    <span><i class="la la-book me-1 text-primary"></i>{{ $category->premium_courses_count }} courses</span>
                                    <span><i class="la la-sitemap me-1 text-primary"></i>{{ $category->subcategories_count }} subcategories</span>
                                </div>
                                <a href="{{ route('courses.category.show', ['category' => $category->slug]) }}" class="btn theme-btn w-100">
                                    Explore Category <i class="la la-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            No categories matched your search. Try a different keyword or clear the filters.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="pt-4">
                {{ $categories->onEachSide(1)->links() }}
            </div>
        </div>
    </section>
@endsection

@extends('frontend.app')

@section('content')
<section class="py-5 text-white" style="background:linear-gradient(120deg,#001d42,#0f3c7a);">
    <div class="container text-center">
        <p class="text-uppercase small mb-2">Live Learning</p>
        <h1 class="display-6 fw-semibold mb-3">Upcoming Webinars</h1>
        <p class="mb-0 text-white-50">Join our experts and partner universities for live masterclasses, Q&A sessions, and program deep dives.</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            @forelse($webinners as $item)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary text-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->date }}</strong>
                                    <div class="small text-white-50">{{ $item->time }} EST</div>
                                </div>
                                <span class="badge bg-light text-primary">Live</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h3 class="h5 text-primary">{{ $item->title }}</h3>
                            <p class="text-muted mb-4">{{ $item->from }}</p>
                            <div class="mt-auto">
                                <a href="{{ $item->link }}" target="_blank" rel="noopener" class="btn theme-btn w-100">
                                    Register Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">No webinars are scheduled right now. Check back soon!</div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

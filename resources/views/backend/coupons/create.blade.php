@extends('backend.app')

@section('content')
<div class="content-wrapper">
    @include('backend.topnav')

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-3">
                <div class="col-lg-10">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Create Coupon</h3>
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger mx-3 mt-3">
                                    <strong>We found some issues:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @include('backend.coupons._form', ['coupon' => $coupon])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

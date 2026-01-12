@extends('backend.app')

@section('content')
<div class="content-wrapper">
    @include('backend.topnav')

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-3">
                <div class="col-lg-10">
                    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Edit Coupon</h3>
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success mx-3 mt-3">
                                    {{ session('success') }}
                                </div>
                            @endif

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

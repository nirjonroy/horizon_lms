@extends('backend.app')

@section('content')
<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Edit Access Plan</h3>
                <a href="{{ route('admin.ebook-access-plans.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <form action="{{ route('admin.ebook-access-plans.update', $plan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('backend.ebook_access_plans._form')
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

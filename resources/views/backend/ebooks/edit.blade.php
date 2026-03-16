@extends('backend.app')

@section('content')
<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Edit E-Book</h3>
                <a href="{{ route('admin.ebooks.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <form action="{{ route('admin.ebooks.update', $ebook) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('backend.ebooks._form')
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update E-Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

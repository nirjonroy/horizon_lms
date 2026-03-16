@extends('backend.app')

@section('content')
<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add E-Book</h3>
            </div>
            <form action="{{ route('admin.ebooks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('backend.ebooks._form')
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save E-Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

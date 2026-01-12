@extends('backend.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Partner</h3>
            </div>
            <form action="{{ route('admin.partners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="website_url">Website URL</label>
                        <input type="url" name="website_url" id="website_url" value="{{ old('website_url') }}" class="form-control @error('website_url') is-invalid @enderror">
                        @error('website_url')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" name="display_order" id="display_order" value="{{ old('display_order', 0) }}" class="form-control @error('display_order') is-invalid @enderror">
                        @error('display_order')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control-file @error('logo') is-invalid @enderror">
                        @error('logo')
                            <span class="text-danger small d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Visible on home page</label>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

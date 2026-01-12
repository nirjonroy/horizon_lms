@extends('backend.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Testimonial</h3>
            </div>
            <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $testimonial->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role">Role / Title</label>
                        <input type="text" name="role" id="role" value="{{ old('role', $testimonial->role) }}" class="form-control @error('role') is-invalid @enderror">
                        @error('role')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="rating">Rating (1-5)</label>
                        <input type="number" name="rating" id="rating" min="1" max="5" value="{{ old('rating', $testimonial->rating) }}" class="form-control @error('rating') is-invalid @enderror" required>
                        @error('rating')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" rows="4" class="form-control @error('message') is-invalid @enderror" required>{{ old('message', $testimonial->message) }}</textarea>
                        @error('message')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $testimonial->display_order) }}" class="form-control @error('display_order') is-invalid @enderror">
                        @error('display_order')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="avatar">Avatar</label>
                        @if($testimonial->avatar)
                            <div class="mb-2">
                                <img src="{{ asset($testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="img-size-64">
                            </div>
                        @endif
                        <input type="file" name="avatar" id="avatar" class="form-control-file @error('avatar') is-invalid @enderror">
                        @error('avatar')
                            <span class="text-danger small d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Visible on home page</label>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

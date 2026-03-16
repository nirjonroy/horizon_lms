@extends('backend.app')

@section('content')
<div class="container">
    <div class="col-md-10">
        <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Edit E-Book Category</h3>
                <a href="{{ route('admin.ebook-categories.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <form action="{{ route('admin.ebook-categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
                        @error('slug')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Current Image</label><br>
                        @if($category->image)
                            <img
                                src="{{ filter_var($category->image, FILTER_VALIDATE_URL) ? $category->image : asset($category->image) }}"
                                alt="{{ $category->name }}"
                                style="max-height: 120px;"
                                class="mb-2"
                            >
                        @else
                            <span class="text-muted">No image uploaded.</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Replace Image</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ (string) old('status', (int) $category->status) === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (string) old('status', (int) $category->status) === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

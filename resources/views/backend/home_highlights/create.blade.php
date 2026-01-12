@extends('backend.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Highlight</h3>
            </div>
            <form action="{{ route('admin.home-highlights.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" class="form-control @error('subtitle') is-invalid @enderror">
                        @error('subtitle')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="icon_class">Icon Class</label>
                        <input type="text" name="icon_class" id="icon_class" value="{{ old('icon_class') }}" class="form-control @error('icon_class') is-invalid @enderror" placeholder="e.g. la la-star">
                        @error('icon_class')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows=3 class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
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

                    <div class="form-group form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Visible on home page</label>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.home-highlights.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

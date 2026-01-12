@extends('backend.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Subcategory</h3>
                    <a href="{{ route('admin.premium-course-subcategories.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <form action="{{ route('admin.premium-course-subcategories.update', $subcategory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="subcategory-category">Category</label>
                            <select name="category_id" id="subcategory-category" class="form-control" required>
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $subcategory->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="subcategory-name">Name</label>
                            <input type="text" id="subcategory-name" name="name" class="form-control" value="{{ old('name', $subcategory->name) }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="subcategory-slug">Slug</label>
                            <input type="text"  name="slug" class="form-control" value="{{ old('slug', $subcategory->slug) }}">
                            @error('slug')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="subcategory-description">Description</label>
                            <textarea id="subcategory-description" name="description" rows="3" class="form-control textarea">{{ old('description', $subcategory->description) }}</textarea>
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="subcategory-image">Thumbnail</label>
                            @if($subcategory->image)
                                <div class="mb-2">
                                    <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }} image" class="rounded" style="width:120px;height:90px;object-fit:cover;">
                                </div>
                            @endif
                            <input type="file" id="subcategory-image" name="image" class="form-control-file" accept="image/*">
                            <small class="form-text text-muted">Leave blank to keep current image.</small>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h5 class="text-primary mb-3">SEO Settings</h5>
                            <div class="form-group">
                                <label for="subcategory-meta-title">Meta Title</label>
                                <input type="text" id="subcategory-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $subcategory->meta_title) }}">
                                @error('meta_title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-meta-description">Meta Description</label>
                                <textarea id="subcategory-meta-description" name="meta_description" rows="3" class="form-control textarea">{{ old('meta_description', $subcategory->meta_description) }}</textarea>
                                @error('meta_description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-meta-image">Meta Image</label>
                                @if($subcategory->meta_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($subcategory->meta_image) }}" alt="{{ $subcategory->name }} meta image" class="rounded" style="width:120px;height:90px;object-fit:cover;">
                                    </div>
                                @endif
                                <input type="file" id="subcategory-meta-image" name="meta_image" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Leave blank to keep current meta image.</small>
                                @error('meta_image')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-author">Author</label>
                                <input type="text" id="subcategory-author" name="author" class="form-control" value="{{ old('author', $subcategory->author) }}">
                                @error('author')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-publisher">Publisher</label>
                                <input type="text" id="subcategory-publisher" name="publisher" class="form-control" value="{{ old('publisher', $subcategory->publisher) }}">
                                @error('publisher')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-copyright">Copyright</label>
                                <input type="text" id="subcategory-copyright" name="copyright" class="form-control" value="{{ old('copyright', $subcategory->copyright) }}">
                                @error('copyright')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-site-name">Site Name</label>
                                <input type="text" id="subcategory-site-name" name="site_name" class="form-control" value="{{ old('site_name', $subcategory->site_name) }}">
                                @error('site_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-keywords">Keywords</label>
                                <input type="text" id="subcategory-keywords" name="keywords" class="form-control" value="{{ old('keywords', $subcategory->keywords) }}" placeholder="Keyword1, Keyword2, Keyword3">
                                @error('keywords')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Update Subcategory</button>
                        <a href="{{ route('admin.premium-course-subcategories.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('subcategory-name');
        const slugInput = document.getElementById('subcategory-slug');

        const slugify = (value) => value
            .toString()
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        if (nameInput) {
            nameInput.addEventListener('input', () => {
                if (!slugInput.dataset.userEdited) {
                    slugInput.value = slugify(nameInput.value);
                }
            });
        }

        if (slugInput) {
            slugInput.addEventListener('input', () => {
                slugInput.dataset.userEdited = slugInput.value.trim() !== '';
            });
        }
    });
</script>
@endpush
@endsection

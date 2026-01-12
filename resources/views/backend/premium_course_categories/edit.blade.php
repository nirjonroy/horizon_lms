@extends('backend.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Category</h3>
                    <a href="{{ route('admin.premium-course-categories.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <form action="{{ route('admin.premium-course-categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="category-name">Name</label>
                            <input type="text" id="category-name" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="category-slug">Slug</label>
                            <input type="text" id="category-slug" name="slug" class="form-control" value="{{ old('slug', $category->slug) }}">
                            @error('slug')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="category-description">Description</label>
                            <textarea id="category-description" name="description" rows="3" class="form-control textarea">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="show-on-home" name="show_on_homepage" value="1" {{ old('show_on_homepage', $category->show_on_homepage) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show-on-home">Show on Home Page</label>
                        </div>

                        <div class="form-group">
                            <label for="category-image">Thumbnail</label>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }} image" class="rounded" style="width:120px;height:90px;object-fit:cover;">
                                </div>
                            @endif
                            <input type="file" class="form-control-file" id="category-image" name="image" accept="image/*">
                            <small class="form-text text-muted">Leave blank to keep current image.</small>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h5 class="text-primary mb-3">SEO Settings</h5>
                            <div class="form-group">
                                <label for="category-meta-title">Meta Title</label>
                                <input type="text" id="category-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $category->meta_title) }}">
                                @error('meta_title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-meta-description">Meta Description</label>
                                <textarea id="category-meta-description" name="meta_description" rows="3" class="form-control textarea">{{ old('meta_description', $category->meta_description) }}</textarea>
                                @error('meta_description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-meta-image">Meta Image</label>
                                @if($category->meta_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($category->meta_image) }}" alt="{{ $category->name }} meta image" class="rounded" style="width:120px;height:90px;object-fit:cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="category-meta-image" name="meta_image" accept="image/*">
                                <small class="form-text text-muted">Leave blank to keep current meta image.</small>
                                @error('meta_image')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-author">Author</label>
                                <input type="text" id="category-author" name="author" class="form-control" value="{{ old('author', $category->author) }}">
                                @error('author')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-publisher">Publisher</label>
                                <input type="text" id="category-publisher" name="publisher" class="form-control" value="{{ old('publisher', $category->publisher) }}">
                                @error('publisher')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-copyright">Copyright</label>
                                <input type="text" id="category-copyright" name="copyright" class="form-control" value="{{ old('copyright', $category->copyright) }}">
                                @error('copyright')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-site-name">Site Name</label>
                                <input type="text" id="category-site-name" name="site_name" class="form-control" value="{{ old('site_name', $category->site_name) }}">
                                @error('site_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-keywords">Keywords</label>
                                <input type="text" id="category-keywords" name="keywords" class="form-control" value="{{ old('keywords', $category->keywords) }}" placeholder="Keyword1, Keyword2, Keyword3">
                                @error('keywords')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Update Category</button>
                        <a href="{{ route('admin.premium-course-categories.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!--<script>-->
<!--    document.addEventListener('DOMContentLoaded', function () {-->
<!--        const nameInput = document.getElementById('category-name');-->
<!--        const slugInput = document.getElementById('category-slug');-->

<!--        const slugify = (value) => value-->
<!--            .toString()-->
<!--            .trim()-->
<!--            .toLowerCase()-->
<!--            .replace(/[^a-z0-9\s-]/g, '')-->
<!--            .replace(/\s+/g, '-')-->
<!--            .replace(/-+/g, '-');-->

<!--        if (nameInput) {-->
<!--            nameInput.addEventListener('input', () => {-->
<!--                if (!slugInput.dataset.userEdited) {-->
<!--                    slugInput.value = slugify(nameInput.value);-->
<!--                }-->
<!--            });-->
<!--        }-->

<!--        if (slugInput) {-->
<!--            slugInput.addEventListener('input', () => {-->
<!--                slugInput.dataset.userEdited = slugInput.value.trim() !== '';-->
<!--            });-->
<!--        }-->
<!--    });-->
<!--</script>-->
@endpush
@endsection

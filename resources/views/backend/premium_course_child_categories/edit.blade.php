@extends('backend.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Child Category</h3>
                    <a href="{{ route('admin.premium-course-child-categories.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <form action="{{ route('admin.premium-course-child-categories.update', $childCategory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="child-category-parent">Category</label>
                            <select name="category_id" id="child-category-parent" class="form-control" required>
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $childCategory->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-subcategory">Subcategory</label>
                            <select name="subcategory_id" id="child-subcategory" class="form-control" required>
                                <option value="">Select subcategory</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}"
                                        data-category="{{ $subcategory->category_id }}"
                                        {{ old('subcategory_id', $childCategory->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subcategory_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-name">Name</label>
                            <input type="text" id="child-name" name="name" class="form-control" value="{{ old('name', $childCategory->name) }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-slug">Slug</label>
                            <input type="text" id="" name="slug" class="form-control" value="{{ old('slug', $childCategory->slug) }}">
                            @error('slug')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-description">Description</label>
                            <textarea id="child-description" name="description" rows="3" class="form-control textarea">{{ old('description', $childCategory->description) }}</textarea>
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-image">Thumbnail</label>
                            @if($childCategory->image)
                                <div class="mb-2">
                                    <img src="{{ asset($childCategory->image) }}" alt="{{ $childCategory->name }} image" class="rounded" style="width:120px;height:90px;object-fit:cover;">
                                </div>
                            @endif
                            <input type="file" id="child-image" name="image" class="form-control-file" accept="image/*">
                            <small class="form-text text-muted">Leave blank to keep current image.</small>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h5 class="text-primary mb-3">SEO Settings</h5>
                            <div class="form-group">
                                <label for="child-meta-title">Meta Title</label>
                                <input type="text" id="child-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title', $childCategory->meta_title) }}">
                                @error('meta_title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-meta-description">Meta Description</label>
                                <textarea id="child-meta-description" name="meta_description" rows="3" class="form-control textarea">{{ old('meta_description', $childCategory->meta_description) }}</textarea>
                                @error('meta_description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-meta-image">Meta Image</label>
                                @if($childCategory->meta_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($childCategory->meta_image) }}" alt="{{ $childCategory->name }} meta image" class="rounded" style="width:120px;height:90px;object-fit:cover;">
                                    </div>
                                @endif
                                <input type="file" id="child-meta-image" name="meta_image" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Leave blank to keep current meta image.</small>
                                @error('meta_image')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-author">Author</label>
                                <input type="text" id="child-author" name="author" class="form-control" value="{{ old('author', $childCategory->author) }}">
                                @error('author')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-publisher">Publisher</label>
                                <input type="text" id="child-publisher" name="publisher" class="form-control" value="{{ old('publisher', $childCategory->publisher) }}">
                                @error('publisher')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-copyright">Copyright</label>
                                <input type="text" id="child-copyright" name="copyright" class="form-control" value="{{ old('copyright', $childCategory->copyright) }}">
                                @error('copyright')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-site-name">Site Name</label>
                                <input type="text" id="child-site-name" name="site_name" class="form-control" value="{{ old('site_name', $childCategory->site_name) }}">
                                @error('site_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-keywords">Keywords</label>
                                <input type="text" id="child-keywords" name="keywords" class="form-control" value="{{ old('keywords', $childCategory->keywords) }}" placeholder="Keyword1, Keyword2, Keyword3">
                                @error('keywords')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Update Child Category</button>
                        <a href="{{ route('admin.premium-course-child-categories.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('child-category-parent');
        const subcategorySelect = document.getElementById('child-subcategory');
        const nameInput = document.getElementById('child-name');
        const slugInput = document.getElementById('child-slug');

        const slugify = (value) => value
            .toString()
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        const filterSubcategories = () => {
            const selectedCategory = categorySelect.value;
            Array.from(subcategorySelect.options).forEach(option => {
                if (!option.dataset.category) {
                    option.hidden = false;
                    return;
                }

                const shouldShow = !selectedCategory || option.dataset.category === selectedCategory;
                option.hidden = !shouldShow;
                if (!shouldShow && option.selected) {
                    option.selected = false;
                }
            });
        };

        if (categorySelect) {
            categorySelect.addEventListener('change', filterSubcategories);
        }
        filterSubcategories();

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

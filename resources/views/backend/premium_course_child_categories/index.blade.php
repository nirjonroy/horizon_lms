@extends('backend.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Create Child Category</h3>
                </div>
                <form action="{{ route('admin.premium-course-child-categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="child-category-parent">Category</label>
                            <select name="category_id" id="child-category-parent" class="form-control" required>
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
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
                            <input type="text" id="child-name" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-slug">Slug</label>
                            <input type="text" id="child-slug" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate">
                            @error('slug')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="child-description">Description</label>
                            <textarea id="child-description" name="description" rows="3" class="form-control textarea" placeholder="Optional">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="child-image">Thumbnail</label>
                            <input type="file" id="child-image" name="image" class="form-control-file" accept="image/*">
                            <small class="form-text text-muted">Recommended 600x400px.</small>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h5 class="text-primary mb-3">SEO Settings</h5>
                            <div class="form-group">
                                <label for="child-meta-title">Meta Title</label>
                                <input type="text" id="child-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                                @error('meta_title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-meta-description">Meta Description</label>
                                <textarea id="child-meta-description" name="meta_description" rows="3" class="form-control textarea">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-meta-image">Meta Image</label>
                                <input type="file" id="child-meta-image" name="meta_image" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Ideal 1200x630px.</small>
                                @error('meta_image')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-author">Author</label>
                                <input type="text" id="child-author" name="author" class="form-control" value="{{ old('author') }}">
                                @error('author')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-publisher">Publisher</label>
                                <input type="text" id="child-publisher" name="publisher" class="form-control" value="{{ old('publisher') }}">
                                @error('publisher')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-copyright">Copyright</label>
                                <input type="text" id="child-copyright" name="copyright" class="form-control" value="{{ old('copyright') }}">
                                @error('copyright')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-site-name">Site Name</label>
                                <input type="text" id="child-site-name" name="site_name" class="form-control" value="{{ old('site_name') }}">
                                @error('site_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="child-keywords">Keywords</label>
                                <input type="text" id="child-keywords" name="keywords" class="form-control" value="{{ old('keywords') }}" placeholder="Keyword1, Keyword2, Keyword3">
                                @error('keywords')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Child Category</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Child Categories</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Subcategory</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($childCategories as $index => $childCategory)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>{{ $childCategory->name }}</div>
                                        <small class="text-muted"><code>{{ $childCategory->slug }}</code></small>
                                    </td>
                                    <td>{{ $childCategory->category->name ?? '-' }}</td>
                                    <td>{{ $childCategory->subcategory->name ?? '-' }}</td>
                                    <td>
                                        @if($childCategory->image)
                                            <img src="{{ asset($childCategory->image) }}" alt="{{ $childCategory->name }} image" class="rounded" style="width:70px;height:50px;object-fit:cover;">
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="d-flex">
                                        <a href="{{ route('admin.premium-course-child-categories.edit', $childCategory) }}" class="btn btn-sm btn-warning mr-2">Edit</a>
                                        <form action="{{ route('admin.premium-course-child-categories.destroy', $childCategory) }}" method="POST" onsubmit="return confirm('Delete this child category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No child categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                if (!slugInput.value || slugInput.dataset.userEdited !== 'true') {
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

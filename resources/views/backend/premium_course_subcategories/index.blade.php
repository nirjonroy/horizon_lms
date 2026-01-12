@extends('backend.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Create Subcategory</h3>
                </div>
                <form action="{{ route('admin.premium-course-subcategories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="subcategory-category">Category</label>
                            <select name="category_id" id="subcategory-category" class="form-control" required>
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
                            <label for="subcategory-name">Name</label>
                            <input type="text" id="subcategory-name" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="subcategory-slug">Slug</label>
                            <input type="text" id="subcategory-slug" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate">
                            @error('slug')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="subcategory-description">Description</label>
                            <textarea id="subcategory-description" name="description" rows="3" class="form-control textarea" placeholder="Optional">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="subcategory-image">Thumbnail</label>
                            <input type="file" id="subcategory-image" name="image" class="form-control-file" accept="image/*">
                            <small class="form-text text-muted">Recommended 600x400px.</small>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h5 class="text-primary mb-3">SEO Settings</h5>
                            <div class="form-group">
                                <label for="subcategory-meta-title">Meta Title</label>
                                <input type="text" id="subcategory-meta-title" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                                @error('meta_title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-meta-description">Meta Description</label>
                                <textarea id="subcategory-meta-description" name="meta_description" rows="3" class="form-control textarea">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-meta-image">Meta Image</label>
                                <input type="file" id="subcategory-meta-image" name="meta_image" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Ideal 1200x630px.</small>
                                @error('meta_image')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-author">Author</label>
                                <input type="text" id="subcategory-author" name="author" class="form-control" value="{{ old('author') }}">
                                @error('author')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-publisher">Publisher</label>
                                <input type="text" id="subcategory-publisher" name="publisher" class="form-control" value="{{ old('publisher') }}">
                                @error('publisher')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-copyright">Copyright</label>
                                <input type="text" id="subcategory-copyright" name="copyright" class="form-control" value="{{ old('copyright') }}">
                                @error('copyright')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-site-name">Site Name</label>
                                <input type="text" id="subcategory-site-name" name="site_name" class="form-control" value="{{ old('site_name') }}">
                                @error('site_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subcategory-keywords">Keywords</label>
                                <input type="text" id="subcategory-keywords" name="keywords" class="form-control" value="{{ old('keywords') }}" placeholder="Keyword1, Keyword2, Keyword3">
                                @error('keywords')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Subcategory</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Subcategories</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Child Categories</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $index => $subcategory)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div>{{ $subcategory->name }}</div>
                                        <small class="text-muted"><code>{{ $subcategory->slug }}</code></small>
                                    </td>
                                    <td>{{ $subcategory->category->name ?? '-' }}</td>
                                    <td>{{ $subcategory->child_categories_count }}</td>
                                    <td>
                                        @if($subcategory->image)
                                            <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }} image" class="rounded" style="width:70px;height:50px;object-fit:cover;">
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="d-flex">
                                        <a href="{{ route('admin.premium-course-subcategories.edit', $subcategory) }}" class="btn btn-sm btn-warning mr-2">Edit</a>
                                        <form action="{{ route('admin.premium-course-subcategories.destroy', $subcategory) }}" method="POST" onsubmit="return confirm('Delete this subcategory?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No subcategories found.</td>
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

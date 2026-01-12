@extends('backend.app')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-md-5">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Create Category</h3>
                </div>
                <form action="{{ route('admin.premium-course-categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="category-name">Name</label>
                            <input type="text" name="name" id="category-name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category-slug">Slug</label>
                            <input type="text" name="slug" id="category-slug" class="form-control" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate">
                            @error('slug')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category-description">Description</label>
                            <textarea name="description" id="category-description" rows="3" class="form-control textarea" placeholder="Optional">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="show-on-home" name="show_on_homepage" value="1" {{ old('show_on_homepage') ? 'checked' : '' }}>
                            <label class="form-check-label" for="show-on-home">Show on Home Page</label>
                        </div>

                        <div class="form-group">
                            <label for="category-image">Thumbnail</label>
                            <input type="file" class="form-control-file" id="category-image" name="image" accept="image/*">
                            <small class="form-text text-muted">Recommended 600x400px.</small>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top pt-3 mt-4">
                            <h5 class="text-primary mb-3">SEO Settings</h5>
                            <div class="form-group">
                                <label for="category-meta-title">Meta Title</label>
                                <input type="text" name="meta_title" id="category-meta-title" class="form-control" value="{{ old('meta_title') }}">
                                @error('meta_title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-meta-description">Meta Description</label>
                                <textarea name="meta_description" id="category-meta-description" rows="3" class="form-control textarea" placeholder="Short description for search engines">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-meta-image">Meta Image</label>
                                <input type="file" class="form-control-file" id="category-meta-image" name="meta_image" accept="image/*">
                                <small class="form-text text-muted">Ideal for social sharing previews (1200x630px).</small>
                                @error('meta_image')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-author">Author</label>
                                <input type="text" name="author" id="category-author" class="form-control" value="{{ old('author') }}">
                                @error('author')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-publisher">Publisher</label>
                                <input type="text" name="publisher" id="category-publisher" class="form-control" value="{{ old('publisher') }}">
                                @error('publisher')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-copyright">Copyright</label>
                                <input type="text" name="copyright" id="category-copyright" class="form-control" value="{{ old('copyright') }}">
                                @error('copyright')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-site-name">Site Name</label>
                                <input type="text" name="site_name" id="category-site-name" class="form-control" value="{{ old('site_name') }}">
                                @error('site_name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="category-keywords">Keywords</label>
                                <input type="text" name="keywords" id="category-keywords" class="form-control" value="{{ old('keywords') }}" placeholder="Keyword1, Keyword2, Keyword3">
                                @error('keywords')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Categories</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Subcategories</th>
                                <th>Image</th>
                                <th>Home</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td><code>{{ $category->slug }}</code></td>
                                    <td>{{ $category->subcategories_count }}</td>
                                    <td>
                                        @if($category->image)
                                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }} image" class="rounded" style="width:70px;height:50px;object-fit:cover;">
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $category->show_on_homepage ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $category->show_on_homepage ? 'Visible' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="d-flex" style="gap: 8px;">
                                        <form action="{{ route('admin.premium-course-categories.toggle-home', $category) }}" method="POST" class="mr-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $category->show_on_homepage ? 'btn-success' : 'btn-outline-secondary' }}">
                                                {{ $category->show_on_homepage ? 'Hide Home' : 'Show Home' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.premium-course-categories.edit', $category) }}" class="btn btn-sm btn-warning mr-2">Edit</a>
                                        <form action="{{ route('admin.premium-course-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No categories found.</td>
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
        const nameInput = document.getElementById('category-name');
        const slugInput = document.getElementById('category-slug');

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

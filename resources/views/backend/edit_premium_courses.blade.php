@extends('backend.app')

@section('content')

<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Edit Premium Course</h3>
            </div>

            <form role="form" action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    
                    <div class="form-group">
                    <label for="exampleInputFile">Type</label>
                    <select class="form-select form-select-lg mb-3 form-control" aria-label=".form-select-lg example"
                      name="type">
                      <option value="">Open this select menu</option>
            
                      <option value="single" {{ old('type', $course->type) == 'single' ? 'selected' : '' }}>Single</option>
                      <option value="bundle" {{ old('type', $course->type) == 'bundle' ? 'selected' : '' }}>Bundle</option>
                      <option value="monthly" {{ old('type', $course->type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                      <option value="yearly" {{ old('type', $course->type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                      <option value="lifetime" {{ old('type', $course->type) == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                      <option value="weekly" {{ old('type', $course->type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                      <option value="free" {{ old('type', $course->type) == 'free' ? 'selected' : '' }}>Free</option>
                      <option value="free_demo" {{ old('type', $course->type) == 'free_demo' ? 'selected' : '' }}>Free Demo</option>
                    </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="course-category">Category</label>
                                <select name="category_id" id="course-category" class="form-control">
                                    <option value="">Select category</option>
                                    @foreach(($categories ?? collect()) as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="course-subcategory">Subcategory</label>
                                <select name="subcategory_id" id="course-subcategory" class="form-control">
                                    <option value="">Select subcategory</option>
                                    @foreach(($subcategories ?? collect()) as $subcategory)
                                        <option value="{{ $subcategory->id }}"
                                            data-category="{{ $subcategory->category_id }}"
                                            {{ old('subcategory_id', $course->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcategory_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="course-child-category">Child Category</label>
                                <select name="child_category_id" id="course-child-category" class="form-control">
                                    <option value="">Select child category</option>
                                    @foreach(($childCategories ?? collect()) as $childCategory)
                                        <option value="{{ $childCategory->id }}"
                                            data-subcategory="{{ $childCategory->subcategory_id }}"
                                            {{ old('child_category_id', $course->child_category_id) == $childCategory->id ? 'selected' : '' }}>
                                            {{ $childCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('child_category_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $course->title }}" >
                    </div>

                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="{{ $course->slug }}" >
                    </div>

                    @if($course->meta_image)
                        <div class="mb-3">
                            <label class="d-block">Current Meta Image</label>
                            <img src="{{ asset($course->meta_image) }}" alt="" class="mb-2" style="max-height: 120px;">
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Meta Image</label>
                        <input type="file" class="form-control-file" name="meta_image">
                    </div>

                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title', $course->meta_title) }}" placeholder="Meta title for SEO">
                    </div>

                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="3" placeholder="Short description for search engines">{{ old('meta_description', $course->meta_description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" class="form-control" name="author" value="{{ old('author', $course->author) }}" placeholder="Author name">
                    </div>

                    <div class="form-group">
                        <label>Publisher</label>
                        <input type="text" class="form-control" name="publisher" value="{{ old('publisher', $course->publisher) }}" placeholder="Publisher name">
                    </div>

                    <div class="form-group">
                        <label>Copyright</label>
                        <input type="text" class="form-control" name="copyright" value="{{ old('copyright', $course->copyright) }}" placeholder="Copyright notice">
                    </div>

                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" class="form-control" name="site_name" value="{{ old('site_name', $course->site_name) }}" placeholder="Site name for structured data">
                    </div>

                    <div class="form-group">
                        <label>Keywords</label>
                        <input type="text" class="form-control" name="keywords" value="{{ old('keywords', $course->keywords) }}" placeholder="Keyword1, Keyword2, Keyword3">
                    </div>

                    <div class="form-group">
                        <label>Instructor</label>
                        <input type="text" class="form-control" name="instructor" value="{{ $course->instructor }}">
                    </div>

                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" class="form-control" name="duration" value="{{ $course->duration }}">
                    </div>

                    <div class="form-group">
                        <label>Effort</label>
                        <input type="text" class="form-control" name="effort" value="{{ $course->effort }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Questions</label>
                        <input type="text" class="form-control" name="questions" value="{{ $course->questions }}">
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <input type="text" class="form-control" name="format" value="{{ $course->format }}">
                    </div>
                    <div class="form-group">
                        <label>Link</label>
                        <input type="text" class="form-control" name="link" value="{{ $course->link }}">
                    </div>

                    <div class="form-group">
                        <label>Old Price</label>
                        <input type="number" step="0.01" class="form-control" name="old_price" value="{{ $course->old_price }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="{{ $course->price }}">
                    </div>

                    <div class="form-group">
                        <label>Short Description</label>
                        <textarea class="form-control textarea" name="short_description" rows="3">{{ $course->short_description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Long Description</label>
                        <textarea class="form-control textarea" name="long_description" rows="5">{{ $course->long_description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Image</label><br>
                        @if($course->image)
                            <img src="{{ asset($course->image) }}" height="80" class="mb-2"><br>
                        @endif
                        <input type="file" class="form-control-file" name="image">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" {{ $course->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">Published</label>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
function generateSlug() {
    let title = document.getElementById("title").value;
    let slug = title.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
    document.getElementById("slug").value = slug;
}
</script>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('course-category');
        const subcategorySelect = document.getElementById('course-subcategory');
        const childCategorySelect = document.getElementById('course-child-category');

        const filterSubcategories = () => {
            const selectedCategory = categorySelect ? categorySelect.value : '';
            if (!subcategorySelect) {
                return;
            }

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

            filterChildCategories();
        };

        const filterChildCategories = () => {
            const selectedSubcategory = subcategorySelect ? subcategorySelect.value : '';
            if (!childCategorySelect) {
                return;
            }

            Array.from(childCategorySelect.options).forEach(option => {
                if (!option.dataset.subcategory) {
                    option.hidden = false;
                    return;
                }

                const shouldShow = !selectedSubcategory || option.dataset.subcategory === selectedSubcategory;
                option.hidden = !shouldShow;
                if (!shouldShow && option.selected) {
                    option.selected = false;
                }
            });
        };

        if (categorySelect) {
            categorySelect.addEventListener('change', filterSubcategories);
        }
        if (subcategorySelect) {
            subcategorySelect.addEventListener('change', filterChildCategories);
        }

        filterSubcategories();
    });
</script>
@endpush
@endsection

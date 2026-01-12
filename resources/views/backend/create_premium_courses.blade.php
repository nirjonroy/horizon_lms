@extends('backend.app')

@section('content')

<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Add courses</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="{{route('admin.courses.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
              <div class="card-body">

                <div class="form-group">
                    <label for="exampleInputFile">Type</label>
                <select class="form-select form-select-lg mb-3 form-control" aria-label=".form-select-lg example" name="type">
                    <option value="">Open this select menu</option>

                    <option value="single" {{ old('type') === 'single' ? 'selected' : '' }}>Single</option>
                    <option value="bundle" {{ old('type') === 'bundle' ? 'selected' : '' }}>Bundle</option>
                    <option value="monthly" {{ old('type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ old('type') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    <option value="lifetime" {{ old('type') === 'lifetime' ? 'selected' : '' }}>Life Time</option>
                    <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="free" {{ old('type') === 'free' ? 'selected' : '' }}>Free</option>
                    <option value="free_demo" {{ old('type') === 'free_demo' ? 'selected' : '' }}>Free Demo</option>



                  </select>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="course-category">Category</label>
                            <select name="category_id" id="course-category" class="form-control">
                                <option value="">Select category</option>
                                @foreach(($categories ?? collect()) as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
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
                                        {{ old('child_category_id') == $childCategory->id ? 'selected' : '' }}>
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

                 <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" id="title" class="form-control" required oninput="generateSlug()">
    </div>

    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" id="slug" class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Meta Image</label>
        <input type="file" name="meta_image" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Meta Title</label>
        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="Meta title for SEO">
    </div>

    <div class="mb-3">
        <label class="form-label">Meta Description</label>
        <textarea name="meta_description" class="form-control" rows="3" placeholder="Short description for search engines">{{ old('meta_description') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Author</label>
        <input type="text" name="author" class="form-control" value="{{ old('author') }}" placeholder="Author name">
    </div>

    <div class="mb-3">
        <label class="form-label">Publisher</label>
        <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}" placeholder="Publisher name">
    </div>

    <div class="mb-3">
        <label class="form-label">Copyright</label>
        <input type="text" name="copyright" class="form-control" value="{{ old('copyright') }}" placeholder="Copyright notice">
    </div>

    <div class="mb-3">
        <label class="form-label">Site Name</label>
        <input type="text" name="site_name" class="form-control" value="{{ old('site_name') }}" placeholder="Site name for structured data">
    </div>

    <div class="mb-3">
        <label class="form-label">Keywords</label>
        <input type="text" name="keywords" class="form-control" value="{{ old('keywords') }}" placeholder="Keyword1, Keyword2, Keyword3">
    </div>

    <div class="mb-3">
        <label class="form-label">Instructor</label>
        <input type="text" name="instructor" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Effort</label>
        <input type="text" name="effort" class="form-control">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Questions</label>
        <input type="text" name="questions" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Format</label>
        <input type="text" name="format" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="price" step="0.01" class="form-control">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Old Price</label>
        <input type="number" name="old_price" step="0.01" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Link</label>
        <input type="text" name="link" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label ">Short Description</label>
        <textarea name="short_description" class="form-control textarea"></textarea>
    </div>

    

    <div class="mb-3">
        <label class="form-label">Long Description</label>
        <textarea name="long_description" class="form-control textarea"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>
                
                

              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
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

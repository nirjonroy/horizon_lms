@extends('backend.app')

@section('content')

<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit {{ $page->title }}</h3>
            </div>

            <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="form-group">
                        <label for="pageTitle">Title</label>
                        <input
                            type="text"
                            id="pageTitle"
                            name="title"
                            value="{{ old('title', $page->title) }}"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Page title"
                        >
                        @error('title')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pageSlug">Slug</label>
                        <input
                            type="text"
                            id="pageSlug"
                            name="slug"
                            value="{{ old('slug', $page->slug) }}"
                            class="form-control @error('slug') is-invalid @enderror"
                            placeholder="URL slug (optional)"
                        >
                        <small class="form-text text-muted">
                            The slug will be used for the page URL. Leave empty to auto-generate.
                        </small>
                        @error('slug')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pageContent">Content</label>
                        <div class="mb-3">
                            <textarea
                                id="pageContent"
                                class="textarea"
                                name="content"
                                style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                            >{!! old('content', $page->content) !!}</textarea>
                        </div>
                        @error('content')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="isPublished"
                            name="is_published"
                            value="1"
                            {{ old('is_published', $page->is_published) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="isPublished">Published</label>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to list
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Page
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('pageTitle');
        const slugInput = document.getElementById('pageSlug');

        if (!titleInput || !slugInput) {
            return;
        }

        let isProgrammaticUpdate = false;
        let isSlugManuallyEdited = slugInput.value.trim().length > 0;

        const slugify = (value) => value
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[\s\W-]+/g, '-')
            .replace(/^-+|-+$/g, '');

        const applyAutoSlug = () => {
            if (isSlugManuallyEdited) {
                return;
            }

            const generated = slugify(titleInput.value);

            isProgrammaticUpdate = true;
            slugInput.value = generated;
            isProgrammaticUpdate = false;
        };

        titleInput.addEventListener('input', applyAutoSlug);

        slugInput.addEventListener('input', () => {
            if (isProgrammaticUpdate) {
                return;
            }

            isSlugManuallyEdited = slugInput.value.trim().length > 0;
        });

        titleInput.addEventListener('blur', applyAutoSlug);

        if (!slugInput.value.trim()) {
            applyAutoSlug();
        }
    });
</script>

@endsection


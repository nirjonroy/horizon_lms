@php
    $ebook = $ebook ?? null;
@endphp

<div class="card-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('category_id', $ebook?->category_id) === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status', (int) ($ebook?->status ?? 1)) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (string) old('status', (int) ($ebook?->status ?? 1)) === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $ebook?->title) }}" required>
                @error('title')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $ebook?->slug) }}" placeholder="Optional">
                @error('slug')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" class="form-control" value="{{ old('author', $ebook?->author) }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $ebook?->isbn) }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Language</label>
                <input type="text" name="language" class="form-control" value="{{ old('language', $ebook?->language) }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Pages</label>
                <input type="text" name="pages" class="form-control" value="{{ old('pages', $ebook?->pages) }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Format</label>
                <input type="text" name="format" class="form-control" value="{{ old('format', $ebook?->format) }}" placeholder="PDF, EPUB, Paperback">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Published At</label>
                <input
                    type="datetime-local"
                    name="published_at"
                    class="form-control"
                    value="{{ old('published_at', optional($ebook?->published_at)->format('Y-m-d\\TH:i')) }}"
                >
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $ebook?->price) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Old Price</label>
                <input type="number" step="0.01" name="old_price" class="form-control" value="{{ old('old_price', $ebook?->old_price) }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>External URL</label>
        <input type="text" name="external_url" class="form-control" value="{{ old('external_url', $ebook?->external_url) }}" placeholder="Store, landing page, or source link">
    </div>

    <div class="form-group">
        <label>Download URL</label>
        <input type="text" name="download_url" class="form-control" value="{{ old('download_url', $ebook?->download_url) }}" placeholder="Remote download URL if file is hosted elsewhere">
    </div>

    <div class="form-group">
        <label>Short Description</label>
        <textarea name="excerpt" class="form-control textarea" rows="4">{{ old('excerpt', $ebook?->excerpt) }}</textarea>
    </div>

    <div class="form-group">
        <label>Long Description</label>
        <textarea name="description" class="form-control textarea" rows="6">{{ old('description', $ebook?->description) }}</textarea>
    </div>

    @if($ebook?->cover_image)
        <div class="form-group">
            <label>Current Cover Image</label><br>
            <img
                src="{{ filter_var($ebook->cover_image, FILTER_VALIDATE_URL) ? $ebook->cover_image : asset($ebook->cover_image) }}"
                alt="{{ $ebook->title }}"
                style="max-height: 140px;"
                class="mb-2"
            >
        </div>
    @endif
    <div class="form-group">
        <label>Cover Image</label>
        <input type="file" name="cover_image" class="form-control-file">
        @error('cover_image')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    @if($ebook?->ebook_file)
        <div class="form-group">
            <label>Current Uploaded File</label><br>
            <a href="{{ asset($ebook->ebook_file) }}" target="_blank" rel="noopener">Open current file</a>
        </div>
    @endif
    <div class="form-group">
        <label>E-Book File</label>
        <input type="file" name="ebook_file" class="form-control-file">
        @error('ebook_file')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $ebook?->meta_title) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Meta Image</label>
                <input type="file" name="meta_image" class="form-control-file">
                @if($ebook?->meta_image)
                    <div class="mt-2">
                        <img
                            src="{{ filter_var($ebook->meta_image, FILTER_VALIDATE_URL) ? $ebook->meta_image : asset($ebook->meta_image) }}"
                            alt="{{ $ebook->title }}"
                            style="max-height: 100px;"
                        >
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Meta Description</label>
        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $ebook?->meta_description) }}</textarea>
    </div>

    <hr>
    <h5 class="mb-3">Advanced SEO</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>SEO Author</label>
                <input type="text" name="seo_author" class="form-control" value="{{ old('seo_author', $ebook?->seo_author) }}" placeholder="Meta author name">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Publisher</label>
                <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $ebook?->publisher) }}" placeholder="Publisher name">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Copyright</label>
                <input type="text" name="copyright" class="form-control" value="{{ old('copyright', $ebook?->copyright) }}" placeholder="Copyright owner">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Site Name</label>
                <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $ebook?->site_name) }}" placeholder="Horizons Unlimited">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Keywords</label>
        <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $ebook?->keywords) }}" placeholder="Keyword 1, Keyword 2, Keyword 3">
    </div>

    <div class="form-group">
        <label>Robots</label>
        <input type="text" name="robots" class="form-control" value="{{ old('robots', $ebook?->robots ?: 'index, follow') }}" placeholder="index, follow">
    </div>
</div>

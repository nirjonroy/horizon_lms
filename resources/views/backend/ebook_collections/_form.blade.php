<div class="card-body">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $collection->name ?? '') }}" required>
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $collection->slug ?? '') }}" placeholder="Optional">
                @error('slug')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Short Description</label>
        <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $collection->excerpt ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label>Full Description</label>
        <textarea name="description" class="form-control" rows="6">{{ old('description', $collection->description ?? '') }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', isset($collection) && $collection->price !== null ? number_format((float) $collection->price, 2, '.', '') : '') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Old Price</label>
                <input type="number" step="0.01" min="0" name="old_price" class="form-control" value="{{ old('old_price', isset($collection) && $collection->old_price !== null ? number_format((float) $collection->old_price, 2, '.', '') : '') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Access Days</label>
                <input type="number" min="1" name="access_days" class="form-control" value="{{ old('access_days', $collection->access_days ?? '') }}" placeholder="Blank = lifetime">
                <small class="text-muted">Leave empty for lifetime bundle access.</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Cover Image</label>
                <input type="file" name="cover_image" class="form-control-file">
                @error('cover_image')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
                @if(!empty($collection?->cover_image))
                    <div class="mt-2">
                        <img src="{{ asset($collection->cover_image) }}" alt="{{ $collection->name }}" style="max-width: 140px;">
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Featured</label>
                <select name="featured" class="form-control">
                    <option value="1" {{ old('featured', (int) ($collection->featured ?? 0)) === 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('featured', (int) ($collection->featured ?? 0)) === 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status', (int) ($collection->status ?? 1)) === 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', (int) ($collection->status ?? 1)) === 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $collection->sort_order ?? 0) }}">
            </div>
        </div>
    </div>

    @php
        $selectedEbookIds = collect(old('ebook_ids', isset($collection) ? $collection->ebooks->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    @endphp
    <div class="form-group">
        <label>Books in This Bundle</label>
        <select name="ebook_ids[]" class="form-control" multiple size="12">
            @foreach($ebooks as $ebook)
                <option value="{{ $ebook->id }}" {{ in_array((int) $ebook->id, $selectedEbookIds, true) ? 'selected' : '' }}>
                    {{ $ebook->title }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl or Cmd to select multiple books.</small>
        @error('ebook_ids')
            <span class="text-danger small d-block">{{ $message }}</span>
        @enderror
        @error('ebook_ids.*')
            <span class="text-danger small d-block">{{ $message }}</span>
        @enderror
    </div>
</div>

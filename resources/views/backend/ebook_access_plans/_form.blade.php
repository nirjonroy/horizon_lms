<div class="card-body">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required>
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $plan->slug ?? '') }}" placeholder="Optional">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label>Tagline</label>
                <input type="text" name="tagline" class="form-control" value="{{ old('tagline', $plan->tagline ?? '') }}" placeholder="Optional sales label">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Billing Cycle</label>
                <input type="text" name="billing_cycle" class="form-control" value="{{ old('billing_cycle', $plan->billing_cycle ?? 'month') }}" placeholder="month, year, lifetime">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Duration Days</label>
                <input type="number" min="1" name="duration_days" class="form-control" value="{{ old('duration_days', $plan->duration_days ?? '') }}" placeholder="Blank = unlimited">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Short Description</label>
        <textarea name="short_description" class="form-control" rows="3">{{ old('short_description', $plan->short_description ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label>Full Description</label>
        <textarea name="description" class="form-control" rows="6">{{ old('description', $plan->description ?? '') }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Access Scope</label>
                <select name="access_scope" class="form-control">
                    @php($selectedScope = old('access_scope', $plan->access_scope ?? \App\Models\EbookAccessPlan::SCOPE_ALL_EBOOKS))
                    <option value="{{ \App\Models\EbookAccessPlan::SCOPE_ALL_EBOOKS }}" {{ $selectedScope === \App\Models\EbookAccessPlan::SCOPE_ALL_EBOOKS ? 'selected' : '' }}>All E-Books</option>
                    <option value="{{ \App\Models\EbookAccessPlan::SCOPE_COLLECTION }}" {{ $selectedScope === \App\Models\EbookAccessPlan::SCOPE_COLLECTION ? 'selected' : '' }}>Specific Bundle Collection</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Linked Collection</label>
                @php($selectedCollectionId = old('ebook_collection_id', $plan->ebook_collection_id ?? ''))
                <select name="ebook_collection_id" class="form-control">
                    <option value="">None</option>
                    @foreach($collections as $collection)
                        <option value="{{ $collection->id }}" {{ (string) $selectedCollectionId === (string) $collection->id ? 'selected' : '' }}>
                            {{ $collection->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Required when the plan unlocks only one bundle collection.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control-file">
                @if(!empty($plan?->image))
                    <div class="mt-2">
                        <img src="{{ asset($plan->image) }}" alt="{{ $plan->name }}" style="max-width: 140px;">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', isset($plan) && $plan->price !== null ? number_format((float) $plan->price, 2, '.', '') : '') }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Old Price</label>
                <input type="number" step="0.01" min="0" name="old_price" class="form-control" value="{{ old('old_price', isset($plan) && $plan->old_price !== null ? number_format((float) $plan->old_price, 2, '.', '') : '') }}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Featured</label>
                <select name="featured" class="form-control">
                    <option value="1" {{ old('featured', (int) ($plan->featured ?? 0)) === 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('featured', (int) ($plan->featured ?? 0)) === 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status', (int) ($plan->status ?? 1)) === 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', (int) ($plan->status ?? 1)) === 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
            </div>
        </div>
    </div>
</div>

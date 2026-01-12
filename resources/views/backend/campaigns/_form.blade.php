@php
    $startsValue = old('starts_at', optional($campaign->starts_at)->format('Y-m-d\TH:i'));
    $endsValue = old('ends_at', optional($campaign->ends_at)->format('Y-m-d\TH:i'));
    $selectedTypes = collect(old('target_types', $campaign->target_types ?? []))->filter()->all();
@endphp

<div class="card-body p-4">
    <div class="form-group">
        <label for="name">Campaign Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $campaign->name) }}" placeholder="Spring Accelerator">
        @error('name')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    <p class="text-muted small mb-4">Select at least one course type or category. Leave whichever field empty to ignore it.</p>

    <!--<div class="form-group">-->
    <!--    <label for="slug">Slug</label>-->
    <!--    <input type="text" name="slug" id="slug" class="form-control text-lowercase" value="{{ old('slug', $campaign->slug) }}" placeholder="spring-accelerator">-->
    <!--    <small class="text-muted">Optional – used for URLs & tracking.</small>-->
    <!--    @error('slug')-->
    <!--        <small class="text-danger d-block mt-1">{{ $message }}</small>-->
    <!--    @enderror-->
    <!--</div>-->

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="target_types">Course Types</label>
                <select name="target_types[]" id="target_types" class="form-control" multiple size="6">
                    @forelse($courseTypes as $type)
                        <option value="{{ $type }}" {{ in_array($type, $selectedTypes, true) ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @empty
                        <option value="" disabled>No course types detected</option>
                    @endforelse
                </select>
                <small class="text-muted d-block mt-1">Hold Ctrl/Command to select multiple. Uses the <code>type</code> field from premium courses.</small>
                @error('target_types')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            @php
                $selectedCategories = old('target_categories', $campaign->target_categories ?? []);
                $selectedCategories = array_map('intval', (array) $selectedCategories);
            @endphp
            <div class="form-group">
                <label for="target_categories">Course Categories</label>
                <select name="target_categories[]" id="target_categories" class="form-control" multiple size="6">
                    @forelse($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategories ?? [], true) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @empty
                        <option value="" disabled>No categories available</option>
                    @endforelse
                </select>
                <small class="text-muted d-block mt-1">Select categories to include all courses within them.</small>
                @error('target_categories')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
                @error('target_categories.*')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="discount_type">Discount Type</label>
                <select name="discount_type" id="discount_type" class="form-control">
                    <option value="percentage" {{ old('discount_type', $campaign->discount_type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ old('discount_type', $campaign->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed amount</option>
                </select>
                @error('discount_type')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="discount_value">Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="discount_value" id="discount_value" class="form-control" value="{{ old('discount_value', $campaign->discount_value ?? 0) }}">
                @error('discount_value')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="badge_label">Badge Label</label>
                <input type="text" name="badge_label" id="badge_label" class="form-control" value="{{ old('badge_label', $campaign->badge_label) }}" placeholder="Spring Sale">
                <small class="text-muted">Shown on cards & detail pages.</small>
                @error('badge_label')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Optional internal note or promo copy">{{ old('description', $campaign->description) }}</textarea>
        @error('description')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    <hr>
    <h6 class="text-uppercase text-muted font-weight-bold">Schedule</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label for="starts_at">Starts At</label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ $startsValue }}">
                @error('starts_at')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <div class="form-group mb-0">
                <label for="ends_at">Ends At</label>
                <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ $endsValue }}">
                @error('ends_at')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    <div class="custom-control custom-switch mt-4">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="custom-control-input" {{ old('is_active', $campaign->is_active ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label font-weight-bold" for="is_active">Active & eligible</label>
    </div>
    @error('is_active')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
</div>

<div class="card-footer d-flex justify-content-between bg-white">
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary px-4">Save Campaign</button>
</div>

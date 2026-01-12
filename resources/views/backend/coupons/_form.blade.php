@php
    $startsValue = old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i'));
    $expiresValue = old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i'));
@endphp

<div class="card-body">
    <div class="form-group">
        <label for="name">Internal Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $coupon->name) }}" placeholder="Summer Campaign">
        @error('name')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="code">Coupon Code <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control text-uppercase" value="{{ old('code', $coupon->code) }}" placeholder="SAVE20" required>
        @error('code')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="type">Discount Type</label>
            <select name="type" id="type" class="form-control">
                <option value="fixed" {{ old('type', $coupon->type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed amount</option>
                <option value="percentage" {{ old('type', $coupon->type ?? 'fixed') === 'percentage' ? 'selected' : '' }}>Percentage</option>
            </select>
            @error('type')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="amount">Amount <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" value="{{ old('amount', $coupon->amount ?? 0) }}" required>
            @error('amount')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="max_discount">Max Discount</label>
            <input type="number" step="0.01" min="0" name="max_discount" id="max_discount" class="form-control" value="{{ old('max_discount', $coupon->max_discount) }}" placeholder="Optional cap">
            @error('max_discount')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="min_subtotal">Minimum Subtotal</label>
            <input type="number" step="0.01" min="0" name="min_subtotal" id="min_subtotal" class="form-control" value="{{ old('min_subtotal', $coupon->min_subtotal ?? 0) }}" placeholder="0.00">
            @error('min_subtotal')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="usage_limit">Usage Limit</label>
            <input type="number" min="1" name="usage_limit" id="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="Unlimited">
            @error('usage_limit')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="per_user_limit">Per User Limit</label>
            <input type="number" min="1" name="per_user_limit" id="per_user_limit" class="form-control" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" placeholder="Unlimited">
            @error('per_user_limit')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="starts_at">Starts At</label>
            <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ $startsValue }}">
            @error('starts_at')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            <label for="expires_at">Expires At</label>
            <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ $expiresValue }}">
            @error('expires_at')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Internal reminder or public copy">{{ old('notes', $coupon->notes) }}</textarea>
        @error('notes')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group form-check">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
        @error('is_active')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="card-footer text-right">
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">Save Coupon</button>
</div>

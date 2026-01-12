<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('redemptions')
            ->latest()
            ->paginate(20);

        return view('backend.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $coupon = new Coupon([
            'type' => 'fixed',
            'amount' => 0,
            'is_active' => true,
        ]);

        return view('backend.coupons.create', compact('coupon'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        Coupon::create($data);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('backend.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validatedData($request, $coupon);
        $coupon->update($data);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted.');
    }

    protected function validatedData(Request $request, ?Coupon $coupon = null): array
    {
        $amountRule = ['required', 'numeric', 'min:0'];
        if ($request->input('type') === 'percentage') {
            $amountRule[] = 'max:100';
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],
            'type' => ['required', Rule::in(['fixed', 'percentage'])],
            'amount' => $amountRule,
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'min_subtotal' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        foreach (['max_discount', 'min_subtotal', 'usage_limit', 'per_user_limit', 'starts_at', 'expires_at'] as $field) {
            if (!array_key_exists($field, $validated) || $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        foreach (['starts_at', 'expires_at'] as $dateField) {
            if (!empty($validated[$dateField])) {
                $validated[$dateField] = Carbon::parse($validated[$dateField]);
            }
        }

        if (!array_key_exists('min_subtotal', $validated) || is_null($validated['min_subtotal'])) {
            $validated['min_subtotal'] = 0;
        }

        return $validated;
    }
}

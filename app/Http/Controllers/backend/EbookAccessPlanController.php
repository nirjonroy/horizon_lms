<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\EbookAccessPlan;
use App\Models\EbookCollection;
use App\Models\UserEbookAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EbookAccessPlanController extends Controller
{
    public function index()
    {
        $plans = EbookAccessPlan::with('collection')
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $collections = EbookCollection::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('backend.ebook_access_plans.index', compact('plans', 'collections'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePlan($request);

        $plan = new EbookAccessPlan();
        $this->fillPlan($plan, $data, $request);
        $plan->save();

        return redirect()
            ->route('admin.ebook-access-plans.index')
            ->with('success', 'Access plan created successfully.');
    }

    public function edit(EbookAccessPlan $ebookAccessPlan)
    {
        $collections = EbookCollection::where('status', 1)
            ->orWhere('id', $ebookAccessPlan->ebook_collection_id)
            ->orderBy('name')
            ->get();

        return view('backend.ebook_access_plans.edit', [
            'plan' => $ebookAccessPlan,
            'collections' => $collections,
        ]);
    }

    public function update(Request $request, EbookAccessPlan $ebookAccessPlan)
    {
        $data = $this->validatePlan($request, $ebookAccessPlan);

        $this->fillPlan($ebookAccessPlan, $data, $request);
        $ebookAccessPlan->save();

        return redirect()
            ->route('admin.ebook-access-plans.index')
            ->with('success', 'Access plan updated successfully.');
    }

    public function destroy(EbookAccessPlan $ebookAccessPlan)
    {
        $hasAccess = UserEbookAccess::query()
            ->where('source_type', UserEbookAccess::SOURCE_TYPE_PLAN)
            ->where('source_id', $ebookAccessPlan->id)
            ->exists();

        if ($hasAccess) {
            return redirect()
                ->route('admin.ebook-access-plans.index')
                ->with('error', 'This access plan has already been sold. Disable it instead of deleting it.');
        }

        $this->deleteFileIfExists($ebookAccessPlan->image);
        $ebookAccessPlan->delete();

        return redirect()
            ->route('admin.ebook-access-plans.index')
            ->with('success', 'Access plan deleted successfully.');
    }

    private function validatePlan(Request $request, ?EbookAccessPlan $plan = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('ebook_access_plans', 'slug')->ignore($plan?->id),
            ],
            'tagline' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'access_scope' => ['required', Rule::in([EbookAccessPlan::SCOPE_ALL_EBOOKS, EbookAccessPlan::SCOPE_COLLECTION])],
            'ebook_collection_id' => ['nullable', 'integer', 'exists:ebook_collections,id'],
            'billing_cycle' => ['required', 'string', 'max:50'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric'],
            'old_price' => ['nullable', 'numeric'],
            'featured' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);

        if (($data['access_scope'] ?? EbookAccessPlan::SCOPE_ALL_EBOOKS) === EbookAccessPlan::SCOPE_COLLECTION && empty($data['ebook_collection_id'])) {
            throw ValidationException::withMessages([
                'ebook_collection_id' => 'Select a bundle collection when the plan scope is collection access.',
            ]);
        }

        return $data;
    }

    private function fillPlan(EbookAccessPlan $plan, array $data, Request $request): void
    {
        $collectionId = ($data['access_scope'] ?? EbookAccessPlan::SCOPE_ALL_EBOOKS) === EbookAccessPlan::SCOPE_COLLECTION
            ? ($data['ebook_collection_id'] ?? null)
            : null;

        $plan->name = $data['name'];
        $plan->slug = $this->generateUniqueSlug($data['slug'] ?: $data['name'], $plan->id);
        $plan->tagline = $data['tagline'] ?? null;
        $plan->short_description = $data['short_description'] ?? null;
        $plan->description = $data['description'] ?? null;
        $plan->access_scope = $data['access_scope'];
        $plan->ebook_collection_id = $collectionId;
        $plan->billing_cycle = $data['billing_cycle'];
        $plan->duration_days = $data['duration_days'] ?? null;
        $plan->price = $data['price'] ?? null;
        $plan->old_price = $data['old_price'] ?? null;
        $plan->featured = (bool) $data['featured'];
        $plan->sort_order = (int) ($data['sort_order'] ?? 0);
        $plan->status = (bool) $data['status'];

        if ($request->hasFile('image')) {
            $this->deleteFileIfExists($plan->image);
            $plan->image = $this->uploadFile($request->file('image'), 'ebooks/plans');
        }
    }

    private function uploadFile($file, string $directory): string
    {
        $destination = public_path($directory);

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $directory . '/' . $filename;
    }

    private function deleteFileIfExists(?string $path): void
    {
        if (! $path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function generateUniqueSlug(?string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value ?? '') ?: 'ebook-access-plan';
        $slug = $base;
        $counter = 1;

        while (
            EbookAccessPlan::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

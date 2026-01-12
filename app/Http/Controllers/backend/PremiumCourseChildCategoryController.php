<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCourseCategory;
use App\Models\PremiumCourseChildCategory;
use App\Models\PremiumCourseSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class PremiumCourseChildCategoryController extends Controller
{
    private string $imageDirectory = 'uploads/premium-child-categories';

    public function index()
    {
        $categories = PremiumCourseCategory::orderBy('name')->get();
        $subcategories = PremiumCourseSubcategory::orderBy('name')->get();
        $childCategories = PremiumCourseChildCategory::with(['category', 'subcategory'])
            ->orderBy('name')
            ->get();

        return view('backend.premium_course_child_categories.index', compact('categories', 'subcategories', 'childCategories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), null);
        }

        if ($request->hasFile('meta_image')) {
            $data['meta_image'] = $this->uploadImage($request->file('meta_image'), null);
        }

        PremiumCourseChildCategory::create($data);
        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-child-categories.index')
            ->with('success', 'Child category created successfully.');
    }

    public function edit(PremiumCourseChildCategory $premiumCourseChildCategory)
    {
        $categories = PremiumCourseCategory::orderBy('name')->get();
        $subcategories = PremiumCourseSubcategory::orderBy('name')->get();

        return view('backend.premium_course_child_categories.edit', [
            'childCategory' => $premiumCourseChildCategory,
            'categories' => $categories,
            'subcategories' => $subcategories,
        ]);
    }

    public function update(Request $request, PremiumCourseChildCategory $premiumCourseChildCategory)
    {
        $data = $this->validatedData($request, $premiumCourseChildCategory->id);

        $data['image'] = $this->uploadImage($request->file('image'), $premiumCourseChildCategory->image);
        $data['meta_image'] = $this->uploadImage($request->file('meta_image'), $premiumCourseChildCategory->meta_image);

        $premiumCourseChildCategory->update($data);
        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-child-categories.index')
            ->with('success', 'Child category updated successfully.');
    }

    public function destroy(PremiumCourseChildCategory $premiumCourseChildCategory)
    {
        $this->deleteImage($premiumCourseChildCategory->image);
        $this->deleteImage($premiumCourseChildCategory->meta_image);
        $this->flushExploreMenuCache();

        $premiumCourseChildCategory->delete();

        return redirect()
            ->route('admin.premium-course-child-categories.index')
            ->with('success', 'Child category deleted successfully.');
    }

    private function validatedData(Request $request, ?int $childCategoryId = null): array
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:premium_course_categories,id',
            'subcategory_id' => 'required|exists:premium_course_subcategories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
        ]);

        $this->ensureSubcategoryBelongsToCategory(
            (int) $validated['category_id'],
            (int) $validated['subcategory_id']
        );

        $validated['slug'] = $this->generateUniqueSlug($validated['slug'] ?? $validated['name'], $childCategoryId);
        unset($validated['image']);
        unset($validated['meta_image']);

        return $validated;
    }

    private function ensureSubcategoryBelongsToCategory(int $categoryId, int $subcategoryId): void
    {
        $subcategory = PremiumCourseSubcategory::where('id', $subcategoryId)
            ->where('category_id', $categoryId)
            ->first();

        if (! $subcategory) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'Selected subcategory does not belong to the chosen category.',
            ]);
        }
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);

        if ($slug === '') {
            $slug = Str::random(8);
        }

        $originalSlug = $slug;
        $counter = 1;

        while (
            PremiumCourseChildCategory::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function uploadImage(?UploadedFile $file, ?string $previousPath = null): ?string
    {
        if (! $file) {
            return $previousPath;
        }

        $this->deleteImage($previousPath);

        $directory = public_path($this->imageDirectory);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'child-category');
        $filename .= '-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();

        $file->move($directory, $filename);

        return $this->imageDirectory . '/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }

    private function flushExploreMenuCache(): void
    {
        Cache::forget('explore_menu_categories');
    }
}

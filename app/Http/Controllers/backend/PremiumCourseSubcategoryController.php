<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCourseCategory;
use App\Models\PremiumCourseSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class PremiumCourseSubcategoryController extends Controller
{
    private string $imageDirectory = 'uploads/premium-subcategories';

    public function index()
    {
        $categories = PremiumCourseCategory::orderBy('name')->get();
        $subcategories = PremiumCourseSubcategory::with(['category'])
            ->withCount('childCategories')
            ->orderBy('name')
            ->get();

        return view('backend.premium_course_subcategories.index', compact('categories', 'subcategories'));
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

        PremiumCourseSubcategory::create($data);
        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    public function edit(PremiumCourseSubcategory $premiumCourseSubcategory)
    {
        $categories = PremiumCourseCategory::orderBy('name')->get();

        return view('backend.premium_course_subcategories.edit', [
            'subcategory' => $premiumCourseSubcategory,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, PremiumCourseSubcategory $premiumCourseSubcategory)
    {
        $data = $this->validatedData($request, $premiumCourseSubcategory->id);

        $data['image'] = $this->uploadImage($request->file('image'), $premiumCourseSubcategory->image);
        $data['meta_image'] = $this->uploadImage($request->file('meta_image'), $premiumCourseSubcategory->meta_image);

        $premiumCourseSubcategory->update($data);
        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-subcategories.index')
            ->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(PremiumCourseSubcategory $premiumCourseSubcategory)
    {
        $this->deleteImage($premiumCourseSubcategory->image);
        $this->deleteImage($premiumCourseSubcategory->meta_image);
        $this->flushExploreMenuCache();

        $premiumCourseSubcategory->delete();

        return redirect()
            ->route('admin.premium-course-subcategories.index')
            ->with('success', 'Subcategory deleted successfully.');
    }

    private function validatedData(Request $request, ?int $subcategoryId = null): array
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:premium_course_categories,id',
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

        $validated['slug'] = $this->generateUniqueSlug($validated['slug'] ?? $validated['name'], $subcategoryId);
        unset($validated['image']);
        unset($validated['meta_image']);

        return $validated;
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
            PremiumCourseSubcategory::where('slug', $slug)
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

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'subcategory');
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

<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class PremiumCourseCategoryController extends Controller
{
    private string $imageDirectory = 'uploads/premium-categories';

    public function index()
    {
        $categories = PremiumCourseCategory::withCount('subcategories')
            ->orderBy('name')
            ->get();

        return view('backend.premium_course_categories.index', compact('categories'));
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

        PremiumCourseCategory::create($data);
        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(PremiumCourseCategory $premiumCourseCategory)
    {
        return view('backend.premium_course_categories.edit', [
            'category' => $premiumCourseCategory,
        ]);
    }

    public function update(Request $request, PremiumCourseCategory $premiumCourseCategory)
    {
        $data = $this->validatedData($request, $premiumCourseCategory->id);

        $data['image'] = $this->uploadImage($request->file('image'), $premiumCourseCategory->image);
        $data['meta_image'] = $this->uploadImage($request->file('meta_image'), $premiumCourseCategory->meta_image);

        $premiumCourseCategory->update($data);
        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(PremiumCourseCategory $premiumCourseCategory)
    {
        $this->deleteImage($premiumCourseCategory->image);
        $this->deleteImage($premiumCourseCategory->meta_image);
        $this->flushExploreMenuCache();

        $premiumCourseCategory->delete();

        return redirect()
            ->route('admin.premium-course-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleHome(PremiumCourseCategory $premiumCourseCategory)
    {
        $premiumCourseCategory->update([
            'show_on_homepage' => ! $premiumCourseCategory->show_on_homepage,
        ]);

        $message = $premiumCourseCategory->show_on_homepage
            ? 'Category will now appear on the home page.'
            : 'Category removed from the home page.';

        $this->flushExploreMenuCache();

        return redirect()
            ->route('admin.premium-course-categories.index')
            ->with('success', $message);
    }

    private function validatedData(Request $request, ?int $categoryId = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'show_on_homepage' => 'nullable|boolean',
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

        $validated['slug'] = $this->generateUniqueSlug($validated['slug'] ?? $validated['name'], $categoryId);
        $validated['show_on_homepage'] = $request->boolean('show_on_homepage');
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
            PremiumCourseCategory::where('slug', $slug)
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

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'category');
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

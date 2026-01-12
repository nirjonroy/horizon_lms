<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\PremiumCourse;
use App\Models\PremiumCourseCategory;
use App\Models\PremiumCourseChildCategory;
use App\Models\PremiumCourseSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PremiumCourseController extends Controller
{
    public function index()
    {
        $courses = PremiumCourse::with(['category', 'subcategory', 'childCategory'])
            ->latest()
            ->get();

        return view('backend.premium_courses', compact('courses'));
    }

    public function create()
    {
        $categories = PremiumCourseCategory::orderBy('name')->get();
        $subcategories = PremiumCourseSubcategory::orderBy('name')->get();
        $childCategories = PremiumCourseChildCategory::orderBy('name')->get();

        return view('backend.create_premium_courses', compact('categories', 'subcategories', 'childCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'instructor' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'effort' => 'nullable|string|max:255',
            'questions' => 'nullable|string|max:255',
            'format' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'old_price' => 'numeric|nullable',
            'type' => 'required|string|max:255',
            'category_id' => 'nullable|exists:premium_course_categories,id',
            'subcategory_id' => 'nullable|exists:premium_course_subcategories,id',
            'child_category_id' => 'nullable|exists:premium_course_child_categories,id',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|boolean',
            'link' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'keywords' => 'nullable|string',
        ]);

        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $subcategoryId = $request->filled('subcategory_id') ? (int) $request->input('subcategory_id') : null;
        $childCategoryId = $request->filled('child_category_id') ? (int) $request->input('child_category_id') : null;

        $this->ensureValidTaxonomy($categoryId, $subcategoryId, $childCategoryId);

        $data = $request->only([
            'title',
            'instructor',
            'duration',
            'effort',
            'questions',
            'format',
            'price',
            'old_price',
            'type',
            'link',
            'short_description',
            'long_description',
            'status',
            'meta_title',
            'meta_description',
            'author',
            'publisher',
            'copyright',
            'site_name',
        ]);

        $data['category_id'] = $categoryId;
        $data['subcategory_id'] = $subcategoryId;
        $data['child_category_id'] = $childCategoryId;
        $data['slug'] = Str::slug($request->slug ?: $request->title);
        $data['status'] = (int) $request->status;
        $data['keywords'] = $this->normaliseKeywords($request->keywords);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        if ($request->hasFile('meta_image')) {
            $data['meta_image'] = $this->uploadImage($request->file('meta_image'));
        }

        PremiumCourse::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function show($id)
    {
        $course = PremiumCourse::with('modules')->findOrFail($id);
        return view('premium_courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = PremiumCourse::findOrFail($id);
        $categories = PremiumCourseCategory::orderBy('name')->get();
        $subcategories = PremiumCourseSubcategory::orderBy('name')->get();
        $childCategories = PremiumCourseChildCategory::orderBy('name')->get();

        return view('backend.edit_premium_courses', compact('course', 'categories', 'subcategories', 'childCategories'));
    }

    public function update(Request $request, $id)
    {
        $course = PremiumCourse::findOrFail($id);

        $request->validate([
            'category_id' => 'nullable|exists:premium_course_categories,id',
            'subcategory_id' => 'nullable|exists:premium_course_subcategories,id',
            'child_category_id' => 'nullable|exists:premium_course_child_categories,id',
        ]);

        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $subcategoryId = $request->filled('subcategory_id') ? (int) $request->input('subcategory_id') : null;
        $childCategoryId = $request->filled('child_category_id') ? (int) $request->input('child_category_id') : null;

        $this->ensureValidTaxonomy($categoryId, $subcategoryId, $childCategoryId);

        $course->title = $request->title;
        $course->slug = Str::slug($request->slug ?: $request->title);
        $course->instructor = $request->instructor;
        $course->type = $request->type;
        $course->category_id = $categoryId;
        $course->subcategory_id = $subcategoryId;
        $course->child_category_id = $childCategoryId;
        $course->duration = $request->duration;
        $course->effort = $request->effort;
        $course->questions = $request->questions;
        $course->format = $request->format;
        $course->price = $request->price;
        $course->old_price = $request->old_price;
        $course->link = $request->link;
        $course->short_description = $request->short_description;
        $course->long_description = $request->long_description;
        $course->status = $request->has('status') ? 1 : 0;
        $course->meta_title = $request->meta_title;
        $course->meta_description = $request->meta_description;
        $course->author = $request->author;
        $course->publisher = $request->publisher;
        $course->copyright = $request->copyright;
        $course->site_name = $request->site_name;
        $course->keywords = $this->normaliseKeywords($request->keywords);

        $course->meta_image = $this->updateImage($request->file('meta_image'), $course->meta_image);

        if ($request->hasFile('image')) {
            $course->image = $this->updateImage($request->file('image'), $course->image);
        }

        $course->save();

        return redirect()->back()->with('success', 'Premium course updated successfully!');
    }

    private function updateImage($file, $previousImagePath)
    {
        if ($file) {
            $this->deleteImageIfExists($previousImagePath);
            return $this->uploadImage($file);
        }

        return $previousImagePath;
    }

    private function uploadImage($file)
    {
        $directory = 'premium-courses';
        $destinationPath = public_path($directory);

        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = basename($file->getClientOriginalName());
        $file->move($destinationPath, $filename);

        return $directory . '/' . $filename;
    }

    private function deleteImageIfExists($path)
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }

    public function destroy($id)
    {
        $whereToStudy = PremiumCourse::findOrFail($id);

        $this->deleteImageIfExists($whereToStudy->image);
        $this->deleteImageIfExists($whereToStudy->meta_image);

        $whereToStudy->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Record deleted successfully');
    }

    public function toggleStatus(PremiumCourse $course)
    {
        $course->status = $course->status ? 0 : 1;
        $course->save();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course status updated successfully.');
    }

    private function normaliseKeywords(?string $keywords): ?string
    {
        if ($keywords === null) {
            return null;
        }

        $keywordsArray = array_filter(array_map('trim', explode(',', $keywords)));

        if (empty($keywordsArray)) {
            return null;
        }

        return implode(', ', $keywordsArray);
    }

    private function ensureValidTaxonomy(?int $categoryId, ?int $subcategoryId, ?int $childCategoryId): void
    {
        if ($subcategoryId && ! $categoryId) {
            throw ValidationException::withMessages([
                'category_id' => 'Select a category before choosing a subcategory.',
            ]);
        }

        if ($childCategoryId && ! $subcategoryId) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'Select a subcategory before choosing a child category.',
            ]);
        }

        if ($subcategoryId && $categoryId) {
            $subcategory = PremiumCourseSubcategory::where('id', $subcategoryId)
                ->where('category_id', $categoryId)
                ->first();

            if (! $subcategory) {
                throw ValidationException::withMessages([
                    'subcategory_id' => 'Selected subcategory does not belong to the chosen category.',
                ]);
            }
        }

        if ($childCategoryId && $subcategoryId) {
            $childCategory = PremiumCourseChildCategory::where('id', $childCategoryId)
                ->where('subcategory_id', $subcategoryId)
                ->first();

            if (! $childCategory) {
                throw ValidationException::withMessages([
                    'child_category_id' => 'Selected child category does not belong to the chosen subcategory.',
                ]);
            }
        }
    }
}

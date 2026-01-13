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

    public function export()
    {
        $courses = PremiumCourse::with(['category', 'subcategory', 'childCategory'])
            ->orderBy('id')
            ->get();

        $columns = [
            'id',
            'title',
            'slug',
            'instructor',
            'category_id',
            'category_name',
            'subcategory_id',
            'subcategory_name',
            'child_category_id',
            'child_category_name',
            'type',
            'price',
            'old_price',
            'duration',
            'effort',
            'questions',
            'format',
            'link',
            'short_description',
            'long_description',
            'image',
            'meta_title',
            'meta_description',
            'meta_image',
            'author',
            'publisher',
            'copyright',
            'site_name',
            'keywords',
            'status',
        ];

        $filename = 'premium_courses_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($courses, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($courses as $course) {
                fputcsv($handle, [
                    $course->id,
                    $course->title,
                    $course->slug,
                    $course->instructor,
                    $course->category_id,
                    optional($course->category)->name,
                    $course->subcategory_id,
                    optional($course->subcategory)->name,
                    $course->child_category_id,
                    optional($course->childCategory)->name,
                    $course->type,
                    $course->price,
                    $course->old_price,
                    $course->duration,
                    $course->effort,
                    $course->questions,
                    $course->format,
                    $course->link,
                    $course->short_description,
                    $course->long_description,
                    $course->image,
                    $course->meta_title,
                    $course->meta_description,
                    $course->meta_image,
                    $course->author,
                    $course->publisher,
                    $course->copyright,
                    $course->site_name,
                    $course->keywords,
                    $course->status,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if (! $handle) {
            return redirect()->back()->with('success', 'Import failed: unable to read the CSV file.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return redirect()->back()->with('success', 'Import failed: the CSV file is empty.');
        }

        $header = array_map([$this, 'normalizeCsvHeader'], $header);

        $categories = PremiumCourseCategory::query()->select('id', 'name')->get();
        $categoryLookup = array_change_key_case($categories->pluck('id', 'name')->all(), CASE_LOWER);

        $subcategories = PremiumCourseSubcategory::query()->select('id', 'category_id', 'name')->get();
        $subcategoryLookup = array_change_key_case($subcategories->pluck('id', 'name')->all(), CASE_LOWER);
        $subcategoryCategory = $subcategories->pluck('category_id', 'id')->all();

        $childCategories = PremiumCourseChildCategory::query()->select('id', 'category_id', 'subcategory_id', 'name')->get();
        $childCategoryLookup = array_change_key_case($childCategories->pluck('id', 'name')->all(), CASE_LOWER);
        $childCategoryMeta = $childCategories->mapWithKeys(function ($item) {
            return [$item->id => ['category_id' => $item->category_id, 'subcategory_id' => $item->subcategory_id]];
        })->all();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $row = array_slice(array_pad($row, count($header), null), 0, count($header));
            $data = array_combine($header, $row);

            if (! $data || $this->isEmptyCsvRow($data)) {
                $skipped++;
                continue;
            }

            $id = $this->normalizeCsvValue($data['id'] ?? null);
            $title = $this->normalizeCsvValue($data['title'] ?? ($data['name'] ?? null));
            $slug = $this->normalizeCsvValue($data['slug'] ?? null);

            $course = null;
            if ($id) {
                $course = PremiumCourse::find($id);
            }
            if (! $course && $slug) {
                $course = PremiumCourse::where('slug', $slug)->first();
            }

            if (! $course && ! $title) {
                $skipped++;
                $errors[] = "Row {$rowNumber} skipped: missing title.";
                continue;
            }

            if (! $course) {
                $course = new PremiumCourse();
            }

            $categoryId = $this->normalizeCsvValue($data['category_id'] ?? null);
            $categoryName = $this->normalizeCsvValue($data['category_name'] ?? ($data['category'] ?? null));
            if (! $categoryId && $categoryName) {
                $categoryId = $categoryLookup[strtolower($categoryName)] ?? null;
            }

            $subcategoryId = $this->normalizeCsvValue($data['subcategory_id'] ?? null);
            $subcategoryName = $this->normalizeCsvValue($data['subcategory_name'] ?? ($data['subcategory'] ?? null));
            if (! $subcategoryId && $subcategoryName) {
                $subcategoryId = $subcategoryLookup[strtolower($subcategoryName)] ?? null;
            }

            $childCategoryId = $this->normalizeCsvValue($data['child_category_id'] ?? null);
            $childCategoryName = $this->normalizeCsvValue($data['child_category_name'] ?? ($data['child_category'] ?? null));
            if (! $childCategoryId && $childCategoryName) {
                $childCategoryId = $childCategoryLookup[strtolower($childCategoryName)] ?? null;
            }

            if ($childCategoryId && isset($childCategoryMeta[$childCategoryId])) {
                $subcategoryId = $subcategoryId ?? $childCategoryMeta[$childCategoryId]['subcategory_id'];
                $categoryId = $categoryId ?? $childCategoryMeta[$childCategoryId]['category_id'];
            }

            if ($subcategoryId && ! $categoryId) {
                $categoryId = $subcategoryCategory[$subcategoryId] ?? null;
            }

            try {
                $this->ensureValidTaxonomy($categoryId, $subcategoryId, $childCategoryId);
            } catch (ValidationException $e) {
                $skipped++;
                $errors[] = "Row {$rowNumber} skipped: invalid taxonomy.";
                continue;
            }

            $course->title = $title ?? $course->title;
            $course->slug = $slug
                ? $this->generateUniqueSlug($slug, $course->id)
                : ($course->slug ?: $this->generateUniqueSlug($course->title ?? 'course', $course->id));
            $course->instructor = $this->normalizeCsvValue($data['instructor'] ?? null) ?? $course->instructor;
            $course->type = $this->normalizeCsvValue($data['type'] ?? null) ?? ($course->type ?: 'single');
            $course->duration = $this->normalizeCsvValue($data['duration'] ?? null) ?? $course->duration;
            $course->effort = $this->normalizeCsvValue($data['effort'] ?? null) ?? $course->effort;
            $course->questions = $this->normalizeCsvValue($data['questions'] ?? null) ?? $course->questions;
            $course->format = $this->normalizeCsvValue($data['format'] ?? null) ?? $course->format;
            $course->price = $this->normalizeCsvValue($data['price'] ?? null) ?? $course->price;
            $course->old_price = $this->normalizeCsvValue($data['old_price'] ?? null) ?? $course->old_price;
            $course->link = $this->normalizeCsvValue($data['link'] ?? null) ?? $course->link;
            $course->short_description = $this->normalizeCsvValue($data['short_description'] ?? null) ?? $course->short_description;
            $course->long_description = $this->normalizeCsvValue($data['long_description'] ?? null) ?? $course->long_description;
            $course->image = $this->normalizeCsvValue($data['image'] ?? null) ?? $course->image;
            $course->meta_title = $this->normalizeCsvValue($data['meta_title'] ?? null) ?? $course->meta_title;
            $course->meta_description = $this->normalizeCsvValue($data['meta_description'] ?? null) ?? $course->meta_description;
            $course->meta_image = $this->normalizeCsvValue($data['meta_image'] ?? null) ?? $course->meta_image;
            $course->author = $this->normalizeCsvValue($data['author'] ?? null) ?? $course->author;
            $course->publisher = $this->normalizeCsvValue($data['publisher'] ?? null) ?? $course->publisher;
            $course->copyright = $this->normalizeCsvValue($data['copyright'] ?? null) ?? $course->copyright;
            $course->site_name = $this->normalizeCsvValue($data['site_name'] ?? null) ?? $course->site_name;

            $keywords = $this->normalizeCsvValue($data['keywords'] ?? null);
            if ($keywords !== null) {
                $course->keywords = $this->normaliseKeywords($keywords);
            }

            $course->category_id = $categoryId;
            $course->subcategory_id = $subcategoryId;
            $course->child_category_id = $childCategoryId;

            $status = $this->normalizeCsvBoolean($data['status'] ?? null);
            if ($status !== null) {
                $course->status = $status;
            } elseif (! $course->exists && $course->status === null) {
                $course->status = 1;
            }

            $isNew = ! $course->exists;
            $course->save();

            if ($isNew) {
                $created++;
            } else {
                $updated++;
            }
        }

        fclose($handle);

        $message = "Import complete. Created {$created}, updated {$updated}, skipped {$skipped}.";
        if ($errors) {
            $message .= ' Issues: ' . implode(' ', array_slice($errors, 0, 5));
        }

        return redirect()->back()->with('success', $message);
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

    private function generateUniqueSlug(?string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value ?? '') ?: 'course';
        $slug = $base;
        $counter = 1;

        while (
            PremiumCourse::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function normalizeCsvHeader(?string $value): string
    {
        $value = $value ?? '';
        $value = ltrim($value, "\xEF\xBB\xBF");
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    private function normalizeCsvValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeCsvBoolean($value): ?int
    {
        $value = $this->normalizeCsvValue($value);
        if ($value === null) {
            return null;
        }

        $value = strtolower($value);
        if (in_array($value, ['1', 'true', 'yes', 'active'], true)) {
            return 1;
        }
        if (in_array($value, ['0', 'false', 'no', 'inactive'], true)) {
            return 0;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function isEmptyCsvRow(array $data): bool
    {
        foreach ($data as $value) {
            if ($this->normalizeCsvValue($value) !== null) {
                return false;
            }
        }

        return true;
    }
}

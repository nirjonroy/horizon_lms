<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\EbookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EbookCategoryController extends Controller
{
    public function index()
    {
        $categories = EbookCategory::withCount('ebooks')
            ->orderBy('name')
            ->get();

        return view('backend.ebook_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCategory($request);

        $category = new EbookCategory();
        $this->fillCategory($category, $data, $request);
        $category->save();

        $this->flushMenuCache();

        return redirect()
            ->route('admin.ebook-categories.index')
            ->with('success', 'E-book category created successfully.');
    }

    public function edit(EbookCategory $ebookCategory)
    {
        return view('backend.ebook_categories.edit', [
            'category' => $ebookCategory,
        ]);
    }

    public function update(Request $request, EbookCategory $ebookCategory)
    {
        $data = $this->validateCategory($request, $ebookCategory);

        $this->fillCategory($ebookCategory, $data, $request);
        $ebookCategory->save();

        $this->flushMenuCache();

        return redirect()
            ->route('admin.ebook-categories.index')
            ->with('success', 'E-book category updated successfully.');
    }

    public function destroy(EbookCategory $ebookCategory)
    {
        if ($ebookCategory->ebooks()->exists()) {
            return redirect()
                ->route('admin.ebook-categories.index')
                ->with('error', 'Remove or reassign the books in this category before deleting it.');
        }

        $this->deleteFileIfExists($ebookCategory->image);
        $ebookCategory->delete();

        $this->flushMenuCache();

        return redirect()
            ->route('admin.ebook-categories.index')
            ->with('success', 'E-book category deleted successfully.');
    }

    private function validateCategory(Request $request, ?EbookCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('ebook_categories', 'slug')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'boolean'],
        ]);
    }

    private function fillCategory(EbookCategory $category, array $data, Request $request): void
    {
        $category->name = $data['name'];
        $category->slug = $this->generateUniqueSlug($data['slug'] ?: $data['name'], $category->id);
        $category->description = $data['description'] ?? null;
        $category->status = (bool) $data['status'];

        if ($request->hasFile('image')) {
            $this->deleteFileIfExists($category->image);
            $category->image = $this->uploadFile($request->file('image'), 'ebooks/categories');
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
        $base = Str::slug($value ?? '') ?: 'ebook-category';
        $slug = $base;
        $counter = 1;

        while (
            EbookCategory::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function flushMenuCache(): void
    {
        Cache::forget('ebook_menu_categories');
    }
}

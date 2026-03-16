<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\EbookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EbookController extends Controller
{
    public function index()
    {
        $ebooks = Ebook::with('category')
            ->latest('published_at')
            ->latest('id')
            ->get();

        return view('backend.ebooks.index', compact('ebooks'));
    }

    public function create()
    {
        $categories = EbookCategory::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('backend.ebooks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateEbook($request);

        $ebook = new Ebook();
        $this->fillEbook($ebook, $data, $request);
        $ebook->save();

        $this->flushMenuCache();

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-book created successfully.');
    }

    public function edit(Ebook $ebook)
    {
        $categories = EbookCategory::where('status', 1)
            ->orWhere('id', $ebook->category_id)
            ->orderBy('name')
            ->get();

        return view('backend.ebooks.edit', compact('ebook', 'categories'));
    }

    public function update(Request $request, Ebook $ebook)
    {
        $data = $this->validateEbook($request, $ebook);

        $this->fillEbook($ebook, $data, $request);
        $ebook->save();

        $this->flushMenuCache();

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-book updated successfully.');
    }

    public function destroy(Ebook $ebook)
    {
        $this->deleteFileIfExists($ebook->cover_image);
        $this->deleteFileIfExists($ebook->meta_image);
        $this->deleteFileIfExists($ebook->ebook_file);
        $ebook->delete();

        $this->flushMenuCache();

        return redirect()
            ->route('admin.ebooks.index')
            ->with('success', 'E-book deleted successfully.');
    }

    private function validateEbook(Request $request, ?Ebook $ebook = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:ebook_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('ebooks', 'slug')->ignore($ebook?->id),
            ],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'pages' => ['nullable', 'string', 'max:255'],
            'format' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric'],
            'old_price' => ['nullable', 'numeric'],
            'external_url' => ['nullable', 'string', 'max:2048'],
            'download_url' => ['nullable', 'string', 'max:2048'],
            'excerpt' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ebook_file' => ['nullable', 'file', 'mimes:pdf,epub,zip', 'max:20480'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'boolean'],
        ]);
    }

    private function fillEbook(Ebook $ebook, array $data, Request $request): void
    {
        $ebook->category_id = $data['category_id'] ?? null;
        $ebook->title = $data['title'];
        $ebook->slug = $this->generateUniqueSlug($data['slug'] ?: $data['title'], $ebook->id);
        $ebook->author = $data['author'] ?? null;
        $ebook->isbn = $data['isbn'] ?? null;
        $ebook->language = $data['language'] ?? null;
        $ebook->pages = $data['pages'] ?? null;
        $ebook->format = $data['format'] ?? null;
        $ebook->price = $data['price'] ?? null;
        $ebook->old_price = $data['old_price'] ?? null;
        $ebook->external_url = $data['external_url'] ?? null;
        $ebook->download_url = $data['download_url'] ?? null;
        $ebook->excerpt = $data['excerpt'] ?? null;
        $ebook->description = $data['description'] ?? null;
        $ebook->meta_title = $data['meta_title'] ?? $data['title'];
        $ebook->meta_description = $data['meta_description'] ?? Str::limit(strip_tags($data['excerpt'] ?? $data['description'] ?? ''), 155, '');
        $ebook->published_at = $data['published_at'] ?? null;
        $ebook->status = (bool) $data['status'];

        if ($request->hasFile('cover_image')) {
            $this->deleteFileIfExists($ebook->cover_image);
            $ebook->cover_image = $this->uploadFile($request->file('cover_image'), 'ebooks/covers');
        }

        if ($request->hasFile('meta_image')) {
            $this->deleteFileIfExists($ebook->meta_image);
            $ebook->meta_image = $this->uploadFile($request->file('meta_image'), 'ebooks/meta');
        } elseif (! $ebook->meta_image) {
            $ebook->meta_image = $ebook->cover_image;
        }

        if ($request->hasFile('ebook_file')) {
            $this->deleteFileIfExists($ebook->ebook_file);
            $ebook->ebook_file = $this->uploadFile($request->file('ebook_file'), 'ebooks/files');
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
        $base = Str::slug($value ?? '') ?: 'ebook';
        $slug = $base;
        $counter = 1;

        while (
            Ebook::where('slug', $slug)
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

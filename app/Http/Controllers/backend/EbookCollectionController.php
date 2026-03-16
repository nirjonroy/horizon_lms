<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\EbookCollection;
use App\Models\UserEbookAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EbookCollectionController extends Controller
{
    public function index()
    {
        $collections = EbookCollection::withCount('ebooks')
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $ebooks = Ebook::where('status', 1)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('backend.ebook_collections.index', compact('collections', 'ebooks'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCollection($request);

        $collection = new EbookCollection();
        $this->fillCollection($collection, $data, $request);
        $collection->save();
        $collection->ebooks()->sync($data['ebook_ids'] ?? []);

        return redirect()
            ->route('admin.ebook-collections.index')
            ->with('success', 'Bundle collection created successfully.');
    }

    public function edit(EbookCollection $ebookCollection)
    {
        $ebooks = Ebook::where('status', 1)
            ->orWhereIn('id', $ebookCollection->ebooks()->pluck('ebooks.id'))
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('backend.ebook_collections.edit', [
            'collection' => $ebookCollection->load('ebooks:id,title'),
            'ebooks' => $ebooks,
        ]);
    }

    public function update(Request $request, EbookCollection $ebookCollection)
    {
        $data = $this->validateCollection($request, $ebookCollection);

        $this->fillCollection($ebookCollection, $data, $request);
        $ebookCollection->save();
        $ebookCollection->ebooks()->sync($data['ebook_ids'] ?? []);

        return redirect()
            ->route('admin.ebook-collections.index')
            ->with('success', 'Bundle collection updated successfully.');
    }

    public function destroy(EbookCollection $ebookCollection)
    {
        $hasActiveAccess = UserEbookAccess::where('ebook_collection_id', $ebookCollection->id)->exists();
        $hasPlans = $ebookCollection->accessPlans()->exists();

        if ($hasActiveAccess || $hasPlans) {
            return redirect()
                ->route('admin.ebook-collections.index')
                ->with('error', 'This collection is already tied to plans or user access. Disable it instead of deleting it.');
        }

        $this->deleteFileIfExists($ebookCollection->cover_image);
        $ebookCollection->ebooks()->detach();
        $ebookCollection->delete();

        return redirect()
            ->route('admin.ebook-collections.index')
            ->with('success', 'Bundle collection deleted successfully.');
    }

    private function validateCollection(Request $request, ?EbookCollection $collection = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('ebook_collections', 'slug')->ignore($collection?->id),
            ],
            'excerpt' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price' => ['nullable', 'numeric'],
            'old_price' => ['nullable', 'numeric'],
            'access_days' => ['nullable', 'integer', 'min:1'],
            'featured' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
            'ebook_ids' => ['nullable', 'array'],
            'ebook_ids.*' => ['integer', 'exists:ebooks,id'],
        ]);
    }

    private function fillCollection(EbookCollection $collection, array $data, Request $request): void
    {
        $collection->name = $data['name'];
        $collection->slug = $this->generateUniqueSlug($data['slug'] ?: $data['name'], $collection->id);
        $collection->excerpt = $data['excerpt'] ?? null;
        $collection->description = $data['description'] ?? null;
        $collection->price = $data['price'] ?? null;
        $collection->old_price = $data['old_price'] ?? null;
        $collection->access_days = $data['access_days'] ?? null;
        $collection->featured = (bool) $data['featured'];
        $collection->sort_order = (int) ($data['sort_order'] ?? 0);
        $collection->status = (bool) $data['status'];

        if ($request->hasFile('cover_image')) {
            $this->deleteFileIfExists($collection->cover_image);
            $collection->cover_image = $this->uploadFile($request->file('cover_image'), 'ebooks/collections');
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
        $base = Str::slug($value ?? '') ?: 'ebook-collection';
        $slug = $base;
        $counter = 1;

        while (
            EbookCollection::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

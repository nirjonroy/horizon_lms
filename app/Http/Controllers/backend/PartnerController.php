<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function index(): View
    {
        $partners = Partner::orderBy('display_order')
            ->orderBy('name')
            ->paginate(20);

        return view('backend.partners.index', compact('partners'));
    }

    public function create(): View
    {
        return view('backend.partners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['logo_path'] = $this->handleUpload($request, 'logo');

        Partner::create($data);

        return redirect()
            ->route('admin.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    public function edit(Partner $partner): View
    {
        return view('backend.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['logo_path'] = $this->handleUpload($request, 'logo', $partner->logo_path);

        $partner->update($data);

        return redirect()
            ->route('admin.partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->logo_path && file_exists(public_path($partner->logo_path))) {
            @unlink(public_path($partner->logo_path));
        }

        $partner->delete();

        return redirect()
            ->route('admin.partners.index')
            ->with('success', 'Partner removed.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function handleUpload(Request $request, string $field, ?string $current = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }

        $file = $request->file($field);
        $directory = 'uploads/partners';
        $destination = public_path($directory);

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        if ($current && file_exists(public_path($current))) {
            @unlink(public_path($current));
        }

        return $directory . '/' . $filename;
    }
}

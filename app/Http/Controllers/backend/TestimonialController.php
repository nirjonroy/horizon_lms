<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $testimonials = Testimonial::orderByDesc('created_at')->paginate(20);

        return view('backend.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('backend.testimonials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['avatar'] = $this->handleUpload($request, 'avatar');

        Testimonial::create($data);

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('backend.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['avatar'] = $this->handleUpload($request, 'avatar', $testimonial->avatar);

        $testimonial->update($data);

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->avatar && file_exists(public_path($testimonial->avatar))) {
            @unlink(public_path($testimonial->avatar));
        }

        $testimonial->delete();

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial removed.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'message' => ['required', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function handleUpload(Request $request, string $field, ?string $current = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }

        $file = $request->file($field);
        $directory = 'uploads/testimonials';
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

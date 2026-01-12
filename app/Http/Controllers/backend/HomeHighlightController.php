<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeHighlightController extends Controller
{
    public function index(): View
    {
        $highlights = HomeHighlight::orderBy('display_order')
            ->orderBy('title')
            ->paginate(20);

        return view('backend.home_highlights.index', compact('highlights'));
    }

    public function create(): View
    {
        return view('backend.home_highlights.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');

        HomeHighlight::create($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight created successfully.');
    }

    public function edit(HomeHighlight $home_highlight): View
    {
        return view('backend.home_highlights.edit', ['highlight' => $home_highlight]);
    }

    public function update(Request $request, HomeHighlight $home_highlight): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');

        $home_highlight->update($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight updated successfully.');
    }

    public function destroy(HomeHighlight $home_highlight): RedirectResponse
    {
        $home_highlight->delete();

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight removed.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'icon_class' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}

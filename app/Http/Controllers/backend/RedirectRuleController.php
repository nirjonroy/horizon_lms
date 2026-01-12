<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\RedirectRule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RedirectRuleController extends Controller
{
    public function index()
    {
        $redirects = RedirectRule::orderByDesc('updated_at')->paginate(15);

        return view('backend.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('backend.redirects.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        RedirectRule::create($data);

        return redirect()
            ->route('admin.redirects.index')
            ->with('success', 'Redirection rule created successfully.');
    }

    public function edit(RedirectRule $redirect)
    {
        return view('backend.redirects.edit', compact('redirect'));
    }

    public function update(Request $request, RedirectRule $redirect)
    {
        $data = $this->validatedData($request);

        $redirect->update($data);

        return redirect()
            ->route('admin.redirects.index')
            ->with('success', 'Redirection rule updated successfully.');
    }

    public function destroy(RedirectRule $redirect)
    {
        $redirect->delete();

        return redirect()
            ->route('admin.redirects.index')
            ->with('success', 'Redirection rule removed.');
    }

    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'sources' => 'required|array|min:1',
            'sources.*.pattern' => 'nullable|string|max:255',
            'sources.*.match_type' => 'nullable|in:exact,prefix,contains',
            'destination_url' => 'required|string|max:2048',
            'redirect_type' => 'required|in:301,302,307,410,451',
            'is_active' => 'nullable|boolean',
            'case_insensitive' => 'nullable|boolean',
        ]);

        $sources = collect($validated['sources'] ?? [])
            ->map(function ($source) {
                $pattern = $this->normalizePattern((string) ($source['pattern'] ?? ''));

                return [
                    'pattern' => $pattern,
                    'match_type' => $source['match_type'] ?? 'exact',
                ];
            })
            ->filter(fn ($source) => filled($source['pattern']))
            ->values();

        if ($sources->isEmpty()) {
            throw ValidationException::withMessages([
                'sources.0.pattern' => 'Please provide at least one source URL.',
            ]);
        }

        $validated['sources'] = $sources->all();
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['case_insensitive'] = (bool) ($validated['case_insensitive'] ?? false);

        return $validated;
    }

    protected function normalizePattern(string $pattern): string
    {
        $pattern = trim($pattern);

        if ($pattern === '') {
            return '';
        }

        if (str_contains($pattern, '://')) {
            $parts = parse_url($pattern);
            $path = $parts['path'] ?? '/';
            $query = isset($parts['query']) ? '?' . $parts['query'] : '';
            $pattern = ($path === '' ? '/' : $path) . $query;
        } else {
            if ($pattern === '' || $pattern[0] !== '/') {
                $pattern = '/' . ltrim($pattern, '/');
            }
        }

        return $pattern;
    }
}

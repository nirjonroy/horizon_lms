<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\PremiumCourse;
use App\Models\PremiumCourseCategory;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(20);
        $categoryMap = \App\Models\PremiumCourseCategory::pluck('name', 'id');

        return view('backend.campaigns.index', compact('campaigns', 'categoryMap'));
    }

    public function create()
    {
        $campaign = new Campaign([
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'is_active' => true,
            'target_types' => [],
        ]);

        return view('backend.campaigns.create', [
            'campaign' => $campaign,
            'courseTypes' => $this->courseTypes(),
            'categories' => $this->courseCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        Campaign::create($data);
        CampaignService::flush();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function edit(Campaign $campaign)
    {
        return view('backend.campaigns.edit', [
            'campaign' => $campaign,
            'courseTypes' => $this->courseTypes(),
            'categories' => $this->courseCategories(),
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $this->validatedData($request, $campaign);
        $campaign->update($data);
        CampaignService::flush();

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        CampaignService::flush();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'Campaign removed.');
    }

    protected function validatedData(Request $request, ?Campaign $campaign = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('campaigns', 'slug')->ignore($campaign?->id),
            ],
            'target_types' => ['nullable', 'array', 'required_without:target_categories'],
            'target_types.*' => ['string', 'max:255'],
            'target_categories' => ['nullable', 'array', 'required_without:target_types'],
            'target_categories.*' => ['integer'],
            'discount_type' => ['required', Rule::in(['fixed', 'percentage'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'badge_label' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['target_types'] = array_values(array_filter(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data['target_types'] ?? []), fn ($value) => $value !== null && $value !== ''));
        $data['target_categories'] = array_values(array_map('intval', array_filter($data['target_categories'] ?? [], fn ($value) => $value !== null && $value !== '')));
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }

    protected function courseTypes()
    {
        return PremiumCourse::query()
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
    }

    protected function courseCategories()
    {
        return PremiumCourseCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}

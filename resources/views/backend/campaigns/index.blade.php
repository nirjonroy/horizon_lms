@extends('backend.app')

@section('content')
<div class="content-wrapper">
    @include('backend.topnav')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-2">
                <div class="col-lg-7 col-md-6">
                    <h1 class="m-0">Campaigns</h1>
                    <p class="text-muted mb-0 small">Schedule type-based discounts and keep track of current promotions.</p>
                </div>
                <div class="col-lg-5 col-md-6 text-md-right mt-3 mt-md-0">
                    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus mr-1"></i> New Campaign
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header border-0">
                    <h3 class="card-title mb-0">Campaign Library</h3>
                    <!--<small class="text-muted">Based on <code>premium_courses.type</code> values.</small>-->
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-3">Name</th>
                                <th>Target Types</th>
                                <th>Categories</th>
                                <th>Discount</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th class="text-right pr-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                                @php
                                    $now = now();
                                    $isUpcoming = $campaign->starts_at && $campaign->starts_at->isFuture();
                                    $isExpired = $campaign->ends_at && $campaign->ends_at->lt($now);
                                @endphp
                                <tr>
                                    <td class="pl-3 align-middle">
                                        <div class="font-weight-bold">{{ $campaign->name }}</div>
                                        @if($campaign->badge_label)
                                            <small class="text-muted">Badge: {{ $campaign->badge_label }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light text-dark">
                                            {{ implode(', ', $campaign->target_types ?? []) ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($campaign->target_categories)
                                            <span class="badge badge-light text-dark">
                                                {{ collect($campaign->target_categories)->map(fn ($id) => $categoryMap[$id] ?? '')->filter()->implode(', ') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($campaign->discount_type === 'fixed')
                                            -${{ number_format($campaign->discount_value, 2) }}
                                        @else
                                            -{{ rtrim(rtrim(number_format($campaign->discount_value, 2), '0'), '.') }}%
                                        @endif
                                    </td>
                                    <td class="align-middle small text-muted">
                                        @if($campaign->starts_at)
                                            Starts {{ $campaign->starts_at->format('M d, Y H:i') }}<br>
                                        @else
                                            Live now<br>
                                        @endif
                                        @if($campaign->ends_at)
                                            Ends {{ $campaign->ends_at->format('M d, Y H:i') }}
                                        @else
                                            No end date
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if(!$campaign->is_active)
                                            <span class="badge badge-secondary">Disabled</span>
                                        @elseif($isUpcoming)
                                            <span class="badge badge-warning">Scheduled</span>
                                        @elseif($isExpired)
                                            <span class="badge badge-dark">Expired</span>
                                        @else
                                            <span class="badge badge-success">Live</span>
                                        @endif
                                    </td>
                                    <td class="text-right align-middle pr-3">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-light border" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light border text-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted mb-3">No campaigns yet.</p>
                                        <a href="{{ route('admin.campaigns.create') }}" class="btn btn-outline-primary btn-sm">Create a campaign</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0">
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

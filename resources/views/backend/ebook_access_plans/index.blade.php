@extends('backend.app')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Access Plan</h3>
            </div>
            <form action="{{ route('admin.ebook-access-plans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('backend.ebook_access_plans._form')
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Plan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">E-Book Access Plans</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Scope</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td>
                                    <strong>{{ $plan->name }}</strong>
                                    <br><small class="text-muted">{{ $plan->slug }}</small>
                                </td>
                                <td>{{ $plan->scopeLabel() }}</td>
                                <td>${{ number_format((float) ($plan->price ?? 0), 2) }}</td>
                                <td>{{ $plan->durationLabel() }}</td>
                                <td>
                                    <span class="badge {{ $plan->status ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $plan->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="d-flex align-items-center">
                                    <a href="{{ route('admin.ebook-access-plans.edit', $plan) }}" class="btn btn-sm btn-warning mr-2">Edit</a>
                                    <form action="{{ route('admin.ebook-access-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Delete this access plan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No access plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

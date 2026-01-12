@extends('backend.app')

@section('content')
<div class="container-fluid py-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">URL Redirections</h3>
            <a href="{{ route('admin.redirects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Add Redirection
            </a>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 28%">Source URLs</th>
                        <th style="width: 28%">Destination</th>
                        <th style="width: 12%">Type</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 12%">Updated</th>
                        <th style="width: 10%" class="text-right pr-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redirects as $redirect)
                        <tr>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($redirect->sources ?? [] as $source)
                                        <li>
                                            <span class="badge badge-light text-muted text-uppercase mr-1">{{ $source['match_type'] ?? 'exact' }}</span>
                                            <code>{{ $source['pattern'] ?? '' }}</code>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <a href="{{ $redirect->destination_url }}" target="_blank" rel="noopener">
                                    {{ $redirect->destination_url }}
                                </a>
                            </td>
                            <td><span class="badge badge-info">{{ $redirect->redirect_type }}</span></td>
                            <td>
                                @if($redirect->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $redirect->updated_at?->format('M d, Y') }}</td>
                            <td class="text-right pr-3">
                                <a href="{{ route('admin.redirects.edit', $redirect) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.redirects.destroy', $redirect) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this redirect?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No redirections configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($redirects->hasPages())
            <div class="card-footer clearfix">
                {{ $redirects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


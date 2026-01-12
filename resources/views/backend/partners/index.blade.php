@extends('backend.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Partners</h3>
        <a href="{{ route('admin.partners.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Partner
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success m-3">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Website</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $partner)
                    <tr>
                        <td>
                            @if($partner->logo_path)
                                <img src="{{ asset($partner->logo_path) }}" alt="{{ $partner->name }}" class="img-size-64">
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $partner->name ?? '—' }}</td>
                        <td>
                            @if($partner->website_url)
                                <a href="{{ $partner->website_url }}" target="_blank">{{ $partner->website_url }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $partner->display_order }}</td>
                        <td>
                            <span class="badge {{ $partner->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $partner->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this partner?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">No partners found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $partners->links() }}
    </div>
</div>
@endsection

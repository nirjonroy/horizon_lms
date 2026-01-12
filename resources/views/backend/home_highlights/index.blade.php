@extends('backend.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Home Highlights</h3>
        <a href="{{ route('admin.home-highlights.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Highlight
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
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Icon</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($highlights as $highlight)
                    <tr>
                        <td>{{ $highlight->title }}</td>
                        <td>{{ $highlight->subtitle ?? '—' }}</td>
                        <td><code>{{ $highlight->icon_class ?? 'la la-check' }}</code></td>
                        <td>{{ $highlight->display_order }}</td>
                        <td>
                            <span class="badge {{ $highlight->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $highlight->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.home-highlights.edit', $highlight) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.home-highlights.destroy', $highlight) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this highlight?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">No highlights configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $highlights->links() }}
    </div>
</div>
@endsection

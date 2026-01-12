@extends('backend.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Testimonials</h3>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New
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
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $testimonial)
                    <tr>
                        <td>
                            @if($testimonial->avatar)
                                <img src="{{ asset($testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="img-size-64">
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $testimonial->name }}</td>
                        <td>{{ $testimonial->role ?? '—' }}</td>
                        <td>{{ $testimonial->rating }}/5</td>
                        <td>
                            <span class="badge {{ $testimonial->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $testimonial->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td>{{ $testimonial->display_order }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this testimonial?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted p-4">No testimonials found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $testimonials->links() }}
    </div>
</div>
@endsection

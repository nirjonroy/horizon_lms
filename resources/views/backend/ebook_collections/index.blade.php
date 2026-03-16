@extends('backend.app')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Bundle Collection</h3>
            </div>
            <form action="{{ route('admin.ebook-collections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('backend.ebook_collections._form')
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Collection</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bundle Collections</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Books</th>
                            <th>Price</th>
                            <th>Access</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collections as $collection)
                            <tr>
                                <td>
                                    <strong>{{ $collection->name }}</strong>
                                    <br><small class="text-muted">{{ $collection->slug }}</small>
                                </td>
                                <td>{{ $collection->ebooks_count }}</td>
                                <td>${{ number_format((float) ($collection->price ?? 0), 2) }}</td>
                                <td>{{ $collection->accessLabel() }}</td>
                                <td>
                                    <span class="badge {{ $collection->status ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $collection->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="d-flex align-items-center">
                                    <a href="{{ route('admin.ebook-collections.edit', $collection) }}" class="btn btn-sm btn-warning mr-2">Edit</a>
                                    <form action="{{ route('admin.ebook-collections.destroy', $collection) }}" method="POST" onsubmit="return confirm('Delete this bundle collection?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No bundle collections found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

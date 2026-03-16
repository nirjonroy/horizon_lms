@extends('backend.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h3 class="card-title mb-2 mb-sm-0">All E-Books</h3>
            <div class="d-flex flex-wrap align-items-center">
                <a href="{{ route('admin.ebooks.create') }}" class="btn btn-success mr-2 mb-2 mb-sm-0">Add E-Book</a>
                <a href="{{ route('admin.ebook-categories.index') }}" class="btn btn-outline-secondary mr-2 mb-2 mb-sm-0">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ebooks as $ebook)
                    <tr>
                        <td>
                            <strong>{{ $ebook->title }}</strong>
                            @if($ebook->isbn)
                                <br><small class="text-muted">ISBN: {{ $ebook->isbn }}</small>
                            @endif
                        </td>
                        <td>{{ optional($ebook->category)->name ?? 'Uncategorized' }}</td>
                        <td>{{ $ebook->author ?? 'N/A' }}</td>
                        <td>
                            @if($ebook->source_product_id)
                                <span class="badge badge-info">Imported</span>
                            @endif
                            @if($ebook->ebook_file)
                                <br><small>Local file</small>
                            @endif
                            @if($ebook->download_url)
                                <br><small>Remote download</small>
                            @endif
                            @if($ebook->external_url)
                                <br><small>External link</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $ebook->status ? 'badge-success' : 'badge-secondary' }}">
                                {{ $ebook->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="d-flex align-items-center">
                            <a href="{{ route('admin.ebooks.edit', $ebook) }}" class="btn btn-sm btn-warning mr-2">Edit</a>
                            <form action="{{ route('admin.ebooks.destroy', $ebook) }}" method="POST" onsubmit="return confirm('Delete this e-book?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No e-books found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

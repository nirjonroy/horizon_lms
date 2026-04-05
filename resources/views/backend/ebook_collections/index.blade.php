@extends('backend.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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

        <div class="card card-secondary mt-4">
            <div class="card-header">
                <h3 class="card-title">Bulk Import From Server Folder</h3>
            </div>
            <form action="{{ route('admin.ebook-collections.import') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Source Folder Path or Public Drive URL</label>
                        <input type="text" name="source_path" class="form-control" value="{{ old('source_path', $importSuggestedPath ?? 'storage/app/imports/bundle-collections') }}" required>
                        <small class="text-muted d-block mt-2">
                            You can use either a local server folder path or a public Google Drive folder URL. Each ZIP, supported file, or top-level child folder becomes one bundle collection.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Default Price</label>
                                <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Default Old Price</label>
                                <input type="number" step="0.01" min="0" name="old_price" class="form-control" value="{{ old('old_price') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Access Days</label>
                                <input type="number" min="1" name="access_days" class="form-control" value="{{ old('access_days') }}" placeholder="Blank = lifetime">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Featured</label>
                                <select name="featured" class="form-control">
                                    <option value="0" {{ old('featured', '0') === '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('featured') === '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Sort Order Start</label>
                        <input type="number" min="0" name="sort_order_start" class="form-control" value="{{ old('sort_order_start', 0) }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-secondary">Import Bundles</button>
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
                                    @if($collection->bundle_file || $collection->download_url)
                                        <br><small class="text-info">Direct bundle file</small>
                                    @endif
                                </td>
                                <td>{{ $collection->ebooks_count > 0 ? $collection->ebooks_count : 'Direct file' }}</td>
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

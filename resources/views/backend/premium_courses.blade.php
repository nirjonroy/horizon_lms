@extends('backend.app')

@section('content')
 <!-- /.card -->

 <div class="card">
    <div class="card-header">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <h3 class="card-title mb-2 mb-sm-0">All Premium Courses</h3>
        <div class="d-flex flex-wrap align-items-center">
          <a href="{{route('admin.courses.create')}}" class="btn btn-success mr-2 mb-2 mb-sm-0">Add</a>
          <a href="{{ route('admin.courses.export') }}" class="btn btn-outline-primary mr-2 mb-2 mb-sm-0">Export CSV</a>
          <form action="{{ route('admin.courses.import') }}" method="POST" enctype="multipart/form-data" class="form-inline mb-2 mb-sm-0">
            @csrf
            <input type="file" name="csv_file" class="form-control form-control-sm mr-2" accept=".csv,text/csv" required>
            <button type="submit" class="btn btn-primary btn-sm">Import</button>
          </form>
        </div>
      </div>
      <small class="text-muted d-block mt-2">Use Export CSV to download a template for imports.</small>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>SL</th>
          <th>Name</th>
          <th>Instructor</th>
          <th>Category</th>
          <th>Image</th>
          <th>Short Description</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($courses as $key => $course)
        <tr>
          <td>{{$key+1}}</td>
          <td>{{$course->title}}</td>
          <td>{{ $course->instructor ?? '—' }}</td>
          <td>
            {{ $course->category->name ?? '—' }}
            @if($course->subcategory)
                <br><small>{{ $course->subcategory->name }}</small>
            @endif
            @if($course->childCategory)
                <br><small class="text-muted">{{ $course->childCategory->name }}</small>
            @endif
          </td>
          <td><img src="{{asset($course->image)}}" alt="" width="100px" height="100px"></td>
          <td> {!!$course->short_description!!}</td>
          <td class="d-flex align-items-center">
            <a href="{{route('admin.courses.edit', $course->id)}}" class="btn btn-sm btn-warning mr-2"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.courses.toggle', $course) }}" method="POST" class="mr-2">
                @csrf
                <button type="submit" class="btn btn-sm {{ $course->status ? 'btn-success' : 'btn-secondary' }}">
                    {{ $course->status ? 'Active' : 'Inactive' }}
                </button>
            </form>
            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true" ></i></button>
            </form>
          </td>
        </tr>
        @endforeach
        </tbody>

      </table>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->

@endsection

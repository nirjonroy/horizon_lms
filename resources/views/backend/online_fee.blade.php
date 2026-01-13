@extends('backend.app')

@section('content')
 <!-- /.card -->

 <div class="card">
 <div class="card-header">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <h3 class="card-title mb-2 mb-sm-0">Online Fees</h3>
        <div class="d-flex flex-wrap align-items-center">
            <a href="{{route('admin.fees.online.create')}}" class="btn btn-success mr-2 mb-2 mb-sm-0">Add</a>
            <a href="{{route('admin.fees.online.export')}}" class="btn btn-outline-primary mr-2 mb-2 mb-sm-0">Export CSV</a>
            <form action="{{ route('admin.fees.online.import') }}" method="POST" enctype="multipart/form-data" class="form-inline mb-2 mb-sm-0">
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
          <th>University</th>
          <th>Programs</th>
          <th>Total Fee</th>

          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @foreach($fees as $key=>$item)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{ optional($item->university)->name ?: 'N/A' }}</td>
                <td>{{$item->program}}</td>
                <td>{{$item->total_fee}}</td>

                <td>
                    <a href="{{route('admin.fees.online.edit', $item->id)}}" class="btn btn-warning">Edit</a>

                    <form action="{{route('admin.fees.online.destroy', $item->id)}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    
                     @if($item->recommend == 0)
                        <a href="{{route('admin.fees.online.recomand', $item->id)}}" class="btn btn-success">Recommended</a>
                    @else
                        <a href="{{route('admin.fees.online.notRecomand', $item->id)}}" class="btn btn-secondary">Not Recommended</a>
                    @endif
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

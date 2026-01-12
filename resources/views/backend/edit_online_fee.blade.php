@extends('backend.app')

@section('content')

  <div class="container">
    <div class="col-md-12">
    <div class="card card-primary">
      <div class="card-header">
      <h3 class="card-title">Add Fees</h3>
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" action="{{route('admin.fees.online.update', $fee->id)}}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="card-body">

        <div class="form-group">
        <label for="exampleInputFile">Type</label>
        <select class="form-select form-select-lg mb-3 form-control" aria-label=".form-select-lg example"
          name="type">
          <option selected>Open this select menu</option>

          <option value="Online" {{ $fee->type == 'Online' ? 'selected' : '' }}>Online</option>
          <option value="Offline" {{ $fee->type == 'Offline' ? 'selected' : '' }}>Offline</option>
        </select>
        </div>


        <div class="form-group">
        <label for="exampleInputFile">Select Degree </label>
        <select class="form-select form-select-lg mb-3 form-control" aria-label=".form-select-lg example"
          name="degree_id">
          <option selected value="">Open this select menu</option>
          @foreach ($category as $item)
        <option value="{{$item->id}}" {{ $fee->degree_id == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
      @endforeach


        </select>
        </div>

        <div class="form-group">
        <label for="exampleInputFile">Select Degree </label>
        <select class="form-select form-select-lg mb-3 form-control" aria-label=".form-select-lg example"
          name="university_id">
          <option selected value="">Open this select menu</option>
          @foreach ($university as $item)
        <option value="{{$item->id}}" {{ $fee->university_id == $item->id ? 'selected' : '' }}>{{$item->name}}
        </option>
      @endforeach


        </select>
        </div>




        <div class="form-group">
        <label for="exampleInputName"> Program </label>
        <input type="text" class="form-control" id="programInput" placeholder="Add title of program"
          name="program" value="{{$fee->program}}">
        </div>

        <div class="form-group">
        <label for="slugInput">Slug</label>
        <input type="text" class="form-control" id="slugInput" name="slug" placeholder="Auto-generated"
          value="{{ old('slug', $fee->slug) }}">
        <small class="text-muted">Will auto-generate from Program. You can adjust if needed.</small>
        </div>

        <div class="form-group">
        <label for="exampleInputName"> Program (short name) </label>
        <input type="text" class="form-control" id="exampleInputEmail1"
          placeholder="Add title of program short name" name="short_name" value="{{$fee->short_name}}">
        </div>

        <div class="form-group">
        <label for="shortDescription">Short description</label>
        <textarea class="form-control" id="shortDescription" name="short_description" rows="3" placeholder="Short summary shown on the program page">{{ old('short_description', $fee->short_description) }}</textarea>
        </div>

        <div class="form-group">
        <label for="longDescription">Long description</label>
        <textarea class="form-control" id="longDescription" name="long_description" rows="6" placeholder="Detailed program overview">{{ old('long_description', $fee->long_description) }}</textarea>
        </div>

        <div class="form-group">
        <label for="exampleInputName"> Total Fee </label>
        <input type="number" class="form-control" id="exampleInputEmail1" placeholder="Add Total Course Fee"
          name="total_fee" value="{{$fee->total_fee}}">
        </div>

        <div class="form-group">
        <label for="exampleInputName"> Discounted Fee </label>
        <input type="number" class="form-control" id="exampleInputEmail1" placeholder="Add Discounted Fee"
          name="yearly" value="{{$fee->yearly}}">
        </div>

        <div class="form-group">
        <label for="exampleInputName"> Link </label>
        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Add Discounted Fee" name="link"
          value="{{$fee->link}}">
        </div>

        <div class="form-group">
        <label for="syllabusPdf">Syllabus PDF</label>
        <input type="file" class="form-control" id="syllabusPdf" name="syllabus_pdf" accept="application/pdf">
        @if($fee->syllabus_pdf)
            <small class="text-muted d-block mt-2">
                Current file:
                <a href="{{ asset($fee->syllabus_pdf) }}" target="_blank" rel="noopener">View PDF</a>
            </small>
        @endif
        </div>

        <div class="form-group">
        <label for="exampleInputName"> Duration </label>
        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Duration" name="duration"
          value="{{$fee->duration}}">
        </div>





      </div>
      <!-- /.card-body -->

      <div class="card-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
      </form>
    </div>
    </div>
  </div>

@push('scripts')
<script>
    (function() {
        const programInput = document.getElementById('programInput');
        const slugInput = document.getElementById('slugInput');
        if (!programInput || !slugInput) return;
        const slugify = (text) => {
            return text.toString().toLowerCase()
                .trim()
                .replace(/[^a-z0-9\\s-]/g, '')
                .replace(/\\s+/g, '-')
                .replace(/-+/g, '-');
        };
        const updateSlug = () => {
            if (!programInput.value) return;
            slugInput.value = slugify(programInput.value);
        };
        programInput.addEventListener('input', updateSlug);
    })();
</script>
@endpush

@endsection

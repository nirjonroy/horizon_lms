@extends('backend.app')

@section('content')

<div class="container">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Edit Slider</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" action="{{route('admin.slider.update', $slider->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
              <div class="card-body">

                <img src="{{asset($slider->image)}}" alt="" width="100px" height="80px">
                <div class="form-group">
                  <label for="exampleInputFile">Hero Banner</label>
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="exampleInputFile" name="image">
                      <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                    </div>
                    <div class="input-group-append">
                      <span class="input-group-text" id="">Upload</span>
                    </div>
                  </div>
                </div>



                <div class="form-group">
                    <label for="exampleInputName"> Hero Banner Title 1 </label>
                    <div class="mb-3">
                      <textarea class="" placeholder="Place some text here"
                                style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="text_1">{{ old('text_1', $slider->text_1) }}</textarea>
                    </div>

                  </div>

                  <div class="form-group">
                    <label for="exampleInputName"> Hero Banner Title 2 </label>
                    <div class="mb-3">
                      <textarea class="" placeholder="Place some text here"
                                style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="text_2">{{ old('text_2', $slider->text_2) }}</textarea>
                    </div>

                  </div>

                  <div class="form-group">
                    <label for="backgroundColor">Background Color</label>
                    <input type="color" class="form-control" id="backgroundColor" name="background_color" value="{{ old('background_color', $slider->background_color ?? '#f97316') }}">
                  </div>

                  <div class="form-group">
                    <label for="buttonOneText">Primary Button Text</label>
                    <input type="text" class="form-control" id="buttonOneText" name="button_one_text" value="{{ old('button_one_text', $slider->button_one_text) }}">
                  </div>

                  <div class="form-group">
                    <label for="buttonOneLink">Primary Button Link</label>
                    <input type="text" class="form-control" id="buttonOneLink" name="button_one_link" value="{{ old('button_one_link', $slider->button_one_link) }}">
                  </div>

                  <div class="form-group">
                    <label for="buttonTwoText">Secondary Button Text</label>
                    <input type="text" class="form-control" id="buttonTwoText" name="button_two_text" value="{{ old('button_two_text', $slider->button_two_text) }}">
                  </div>

                  <div class="form-group">
                    <label for="buttonTwoLink">Secondary Button Link</label>
                    <input type="text" class="form-control" id="buttonTwoLink" name="button_two_link" value="{{ old('button_two_link', $slider->button_two_link) }}">
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

@endsection

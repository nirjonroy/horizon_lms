@extends('backend.app')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"> SEO  Informations</h3>
        {{-- <a href="{{route('blog.create')}}" class="btn btn-success float-right"></a> --}}
    </div>
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>page_name</th>
                    <th>Meta Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->page_name}}</td>
                    <td>{{$item->seo_title}}</td>
                    <td>
                        <a class="btn btn-warning edit-btn" data-id="{{ $item->id }}">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="seoModal" tabindex="-1" role="dialog" aria-labelledby="seoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seoModalLabel">Edit Meta Settings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="seoForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="seoId">
                    <div class="form-group">
                        <label for="seo_title">Meta Title</label>
                        <input type="text" class="form-control" name="seo_title" id="seo_title" required>
                    </div>
                    <div class="form-group">
                        <label for="seo_description">Meta Description</label>
                        <textarea class="form-control" name="seo_description" id="seo_description" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Meta Image</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        <small class="form-text text-muted">Upload a featured image (leave empty to keep the current one).</small>
                        <div class="mt-3" id="image-preview-container" style="display: none;">
                            <p class="mb-1 font-weight-bold">Current preview:</p>
                            <img src="" alt="Meta image preview" id="image-preview" class="img-fluid rounded border">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="author">Author</label>
                        <input type="text" class="form-control" name="author" id="author">
                    </div>
                    <div class="form-group">
                        <label for="publisher">Publisher</label>
                        <input type="text" class="form-control" name="publisher" id="publisher">
                    </div>
                    <div class="form-group">
                        <label for="copyright">Copyright</label>
                        <input type="text" class="form-control" name="copyright" id="copyright">
                    </div>
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" class="form-control" name="site_name" id="site_name">
                    </div>

                    <div class="form-group">
                      <label for="keywords">Keywords (comma-separated)</label>
                      <input type="text" class="form-control" name="keywords" id="keywords" required>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
  $(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('.edit-btn').on('click', function() {
    var id = $(this).data('id');
    $.ajax({
        url: 'get-seo-setup/' + id,
        method: 'GET',
        success: function(data) {
            console.log(data); 
            $('#seoId').val(data.page.id);
            $('#seo_title').val(data.page.seo_title);
            $('#seo_description').val(data.page.seo_description);
            $('#image').val('');
            $('#author').val(data.page.author || '');
            $('#publisher').val(data.page.publisher || '');
            $('#copyright').val(data.page.copyright || '');
            $('#site_name').val(data.page.site_name || '');
            if (data.image_url) {
                $('#image-preview').attr('src', data.image_url);
                $('#image-preview-container').show();
            } else {
                $('#image-preview').attr('src', '');
                $('#image-preview-container').hide();
            }
            
            // Parse the keywords JSON string into an array
            var keywordsArray = JSON.parse(data.page.keywords || '[]'); // Default to empty array if null
            
            // Set the keywords in the input field
            $('#keywords').val(keywordsArray.join(', ')); // Join array to comma-separated string
            
            $('#seoModal').modal('show');
        }
    });
});

      $('#image').on('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
                $('#image-preview-container').show();
            };
            reader.readAsDataURL(file);
        }
      });

      $('#seoForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var id = $('#seoId').val(); // Get the ID from the hidden input
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        $.ajax({
            url: 'update-seo-setup/' + id,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#seoModal').modal('hide'); // Hide the modal on success
                location.reload(); // Reload the page to see changes
            },
            error: function(xhr) {
                // Handle errors
                alert('Error updating SEO settings: ' + xhr.responseText);
            }
        });
    });

  });
</script>
@endsection

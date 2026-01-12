<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\whereToStudy;
use App\Models\Blog;
use App\Models\internationalStudentLife;
class blogController extends Controller
{
    public function index(){
        $blogs=Blog::where('status',1)->latest()->get();
        return view('backend.blogs', compact('blogs'));
    }

    public function create(){
        $studies = whereToStudy::all();
        $life = internationalStudentLife::all();
        return view('backend.create_blog', compact('studies', 'life'));
    }

    public function store(Request $request){
        $blog = new Blog();

        if($request->hasFile('image')){
            $blog->image = $this->uploadImage($request->file('image'));
        }

        if ($request->hasFile('meta_image')) {
            $blog->meta_image = $this->uploadImage($request->file('meta_image'));
        }

        $blog->where_to_study_id = $request->where_to_study_id;
        $blog->life_style_id = $request->life_style_id;
        $blog->accommodation_id = $request->accommodation_id;
        $blog->homePage = $request->homePage;
        $blog->cover = $request->cover;
        $blog->title = $request->title;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->meta_title = $request->meta_title;
        $blog->meta_description = $request->meta_description;
        $blog->author = $request->author;
        $blog->publisher = $request->publisher;
        $blog->copyright = $request->copyright;
        $blog->site_name = $request->site_name;
        $blog->keywords = $this->normaliseKeywords($request->keywords);



        $blog->save();
        return redirect()->back()->with('success', 'Cretated Successfully');
    }

    public function edit($id){
        $blog = Blog::find($id);
        $studies = whereToStudy::all();
        $life = internationalStudentLife::all();
        return view('backend.edit_blog', compact('blog', 'studies', 'life'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        // Update hero_banner image
        $blog->image = $this->updateImage($request->file('image'), $blog->image);
        $blog->meta_image = $this->updateImage($request->file('meta_image'), $blog->meta_image);




        // Update other fields
        $blog->where_to_study_id = $request->where_to_study_id;
        $blog->life_style_id = $request->life_style_id;
        $blog->accommodation_id = $request->accommodation_id;
        $blog->homePage = $request->homePage;
        $blog->cover = $request->cover;
        $blog->title = $request->title;
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->meta_title = $request->meta_title;
        $blog->meta_description = $request->meta_description;
        $blog->author = $request->author;
        $blog->publisher = $request->publisher;
        $blog->copyright = $request->copyright;
        $blog->site_name = $request->site_name;
        $blog->keywords = $this->normaliseKeywords($request->keywords);


        $blog->save();

        return redirect()->back()->with('success', 'Updated Successfully');
    }

    // Helper function to upload or update an image
    private function updateImage($file, $previousImagePath)
    {
        if ($file) {
            // Delete previous image if it exists
            $this->deleteImageIfExists($previousImagePath);

            // Upload new image
            return $this->uploadImage($file);
        }

        // If no new image is uploaded, return the previous image path
        return $previousImagePath;
    }

    // Helper function to upload an image
    private function uploadImage($file)
    {
        $directory = 'uploads/custom-images/blogs';
        $destinationPath = public_path($directory);

        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = basename($file->getClientOriginalName());
        $file->move($destinationPath, $filename);

        return $directory . '/' . $filename;
    }

    // Helper function to delete an image if it exists
    private function deleteImageIfExists($path)
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }

    public function destroy($id)
{
    $blog = Blog::findOrFail($id);

    // Delete associated images
    $this->deleteImageIfExists($blog->image);
    $this->deleteImageIfExists($blog->meta_image);
    $this->deleteImageIfExists($blog->hero_banner);
    $this->deleteImageIfExists($blog->Sporting_event_image);


    // Delete the record
    $blog->delete();

    return redirect()->back()->with('success', 'Record deleted successfully');
}

    private function normaliseKeywords(?string $keywords): ?string
    {
        if ($keywords === null) {
            return null;
        }

        $keywordsArray = array_filter(array_map('trim', explode(',', $keywords)));

        if (empty($keywordsArray)) {
            return null;
        }

        return implode(', ', $keywordsArray);
    }

}

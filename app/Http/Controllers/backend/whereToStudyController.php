<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\whereToStudy;
use RealRashid\SweetAlert\Facades\Alert;

class whereToStudyController extends Controller
{
    public function index(){
        $studies = whereToStudy::where('status', 1)->get();
        $title = 'Delete !';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        return view('backend.whereToStudy', compact('studies'));
    }

    public function create(){
        return view('backend.create_whereToStudy');
    }

    public function store(Request $request){
        $whereToStudy = new whereToStudy();

        if($request->hasFile('slider1')){
            $whereToStudy->slider1 = $this->uploadImage($request->file('slider1'));
        }

        if($request->hasFile('slider2')){
            $whereToStudy->slider2 = $this->uploadImage($request->file('slider2'));
        }

        if($request->hasFile('slider3')){
            $whereToStudy->slider3 = $this->uploadImage($request->file('slider3'));
        }

        if($request->hasFile('image_1')){
            $whereToStudy->image_1 = $this->uploadImage($request->file('image_1'));
        }

        if($request->hasFile('image_2')){
            $whereToStudy->image_2 = $this->uploadImage($request->file('image_2'));
        }

        if($request->hasFile('image_3')){
            $whereToStudy->image_3 = $this->uploadImage($request->file('image_3'));
        }

        if($request->hasFile('image_4')){
            $whereToStudy->image_4 = $this->uploadImage($request->file('image_4'));
        }

        if($request->hasFile('image_5')){
            $whereToStudy->image_5 = $this->uploadImage($request->file('image_5'));
        }

        $whereToStudy->name = $request->name;
        $slugSource = $request->slug ?: $request->name;
        $whereToStudy->slug = $slugSource ? Str::slug($slugSource) : null;
        $whereToStudy->link = $request->link;
        $whereToStudy->short_description = $request->short_description;
        $whereToStudy->rated = $request->rated;
        $whereToStudy->global_network = $request->global_network;
        $whereToStudy->award = $request->award;
        $whereToStudy->rank = $request->rank;
        $whereToStudy->faq_question_1 = $request->faq_question_1;
        $whereToStudy->faq_answer_1 = $request->faq_answer_1;
        $whereToStudy->faq_question_2 = $request->faq_question_2;
        $whereToStudy->faq_answer_2 = $request->faq_answer_2;
        $whereToStudy->faq_question_3 = $request->faq_question_3;
        $whereToStudy->faq_answer_3 = $request->faq_answer_3;
        $whereToStudy->faq_question_4 = $request->faq_question_4;
        $whereToStudy->faq_answer_4 = $request->faq_answer_4;
        $whereToStudy->faq_question_5 = $request->faq_question_5;
        $whereToStudy->faq_answer_5 = $request->faq_answer_5;
        $whereToStudy->is_done = $request->is_done;
        $whereToStudy->priority = $request->priority;
        $whereToStudy->meta_title = $request->meta_title;
        $whereToStudy->meta_description = $request->meta_description;

        $keywords = $request->keywords;
        $whereToStudy->keywords = $keywords ? json_encode(array_map('trim', explode(',', $keywords))) : null;

        $whereToStudy->save();
        return redirect()->back()->with('success', 'Cretated Successfully');
    }

    public function edit($id){
        $studies = whereToStudy::find($id);

        return view('backend.edit_wheretostudy', compact('studies'));
    }

    public function update(Request $request, $id)
    {
        $whereToStudy = whereToStudy::find($id);

        // Update slider1 image
        $whereToStudy->slider1 = $this->updateImage($request->file('slider1'), $whereToStudy->slider1);

        // Update slider2 image
        $whereToStudy->slider2 = $this->updateImage($request->file('slider2'), $whereToStudy->slider2);

        // Update slider3 image
        $whereToStudy->slider3 = $this->updateImage($request->file('slider3'), $whereToStudy->slider3);

        // Update image_1
        $whereToStudy->image_1 = $this->updateImage($request->file('image_1'), $whereToStudy->image_1);

        // Update image_2
        $whereToStudy->image_2 = $this->updateImage($request->file('image_2'), $whereToStudy->image_2);

        // Update image_3
        $whereToStudy->image_3 = $this->updateImage($request->file('image_3'), $whereToStudy->image_3);

        // Update image_4
        $whereToStudy->image_4 = $this->updateImage($request->file('image_4'), $whereToStudy->image_4);

        // Update image_5
        $whereToStudy->image_5 = $this->updateImage($request->file('image_5'), $whereToStudy->image_5);

        // Update other fields
        $whereToStudy->name = $request->name;
        $slugSource = $request->slug ?: $request->name;
        $whereToStudy->slug = $slugSource ? Str::slug($slugSource) : null;
        $whereToStudy->short_description = $request->short_description;
        $whereToStudy->link = $request->link;
        $whereToStudy->rated = $request->rated;
        $whereToStudy->global_network = $request->global_network;
        $whereToStudy->award = $request->award;
        $whereToStudy->rank = $request->rank;
        $whereToStudy->faq_question_1 = $request->faq_question_1;
        $whereToStudy->faq_answer_1 = $request->faq_answer_1;
        $whereToStudy->faq_question_2 = $request->faq_question_2;
        $whereToStudy->faq_answer_2 = $request->faq_answer_2;
        $whereToStudy->faq_question_3 = $request->faq_question_3;
        $whereToStudy->faq_answer_3 = $request->faq_answer_3;
        $whereToStudy->faq_question_4 = $request->faq_question_4;
        $whereToStudy->faq_answer_4 = $request->faq_answer_4;
        $whereToStudy->faq_question_5 = $request->faq_question_5;
        $whereToStudy->faq_answer_5 = $request->faq_answer_5;
        $whereToStudy->is_done = $request->is_done;
        $whereToStudy->priority = $request->priority;
        $whereToStudy->meta_title = $request->meta_title;
        $whereToStudy->meta_description = $request->meta_description;

        $keywords = $request->keywords;
        $whereToStudy->keywords = $keywords ? json_encode(array_map('trim', explode(',', $keywords))) : null;

        $whereToStudy->save();

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
        $directory = 'uploads/custom-images/where_to_study';
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
    $whereToStudy = whereToStudy::findOrFail($id);

    // Delete associated images
    $this->deleteImageIfExists($whereToStudy->slider1);
    $this->deleteImageIfExists($whereToStudy->slider2);
    $this->deleteImageIfExists($whereToStudy->slider3);
    $this->deleteImageIfExists($whereToStudy->image_1);
    $this->deleteImageIfExists($whereToStudy->image_2);
    $this->deleteImageIfExists($whereToStudy->image_3);
    $this->deleteImageIfExists($whereToStudy->image_4);
    $this->deleteImageIfExists($whereToStudy->image_5);

    // Delete the record
    $whereToStudy->delete();

    return redirect()->route('whereToStudy.index')->with('success', 'Record deleted successfully');
}



}


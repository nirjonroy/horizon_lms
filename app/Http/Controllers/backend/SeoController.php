<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\File;
use Image;

class SeoController extends Controller
{
    
    public function seoSetup()
    {
        $pages = SeoSetting::all();
        return view("backend.seo_setup", compact("pages"));
    }

    public function getSeoSetup($id)
    {
        $page = SeoSetting::find($id);
        $imageUrl = null;
        if ($page && $page->image) {
            $imageUrl = filter_var($page->image, FILTER_VALIDATE_URL)
                ? $page->image
                : asset($page->image);
        }

        return response()->json([
            "page" => $page,
            "image_url" => $imageUrl,
        ], 200);
    }

    public function updateSeoSetup(Request $request, $id)
{
    $page = SeoSetting::find($id);
    if (!$page) {
        return response()->json(['error' => 'SEO page not found.'], 404);
    }

    $request->validate([
        'seo_title' => 'required|string',
        'seo_description' => 'required|string',
        'keywords' => 'nullable|string', // Validate keywords as a string
        'image' => 'nullable|image|max:4096',
        'author' => 'nullable|string',
        'publisher' => 'nullable|string',
        'copyright' => 'nullable|string',
        'site_name' => 'nullable|string',
    ]);

    $page->seo_title = $request->input('seo_title');
    $page->seo_description = $request->input('seo_description');

    if ($request->hasFile('image')) {
        $this->deleteImageIfExists($page->image);
        $page->image = $this->storeSeoImage($request->file('image'), $page->id);
    }

    $page->author = $request->input('author');
    $page->publisher = $request->input('publisher');
    $page->copyright = $request->input('copyright');
    $page->site_name = $request->input('site_name');

    // Convert keywords from comma-separated string to JSON array
    $keywords = $request->input('keywords');
    $page->keywords = $keywords ? json_encode(array_map('trim', explode(',', $keywords))) : null;

    $page->save();

    return response()->json(['success' => 'SEO updated successfully.']);
}

    private function storeSeoImage($file, $id)
    {
        $directory = public_path('uploads/custom-images/seo');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = 'seo-'.$id.'-'.time().'.'.$extension;
        $relativePath = 'uploads/custom-images/seo/'.$fileName;
        Image::make($file)->save(public_path($relativePath));

        return $relativePath;
    }

    private function deleteImageIfExists($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}

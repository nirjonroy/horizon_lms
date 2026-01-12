<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\onlineFee;
use App\Models\feesCategory;
use App\Models\whereToStudy;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Illuminate\Support\Str;

class onlineFeeController extends Controller
{
    public function index(){
            $fees = onlineFee::where('status',1)->latest()->get();

        return view('backend.online_fee', compact('fees'));
    }
    public function create(){
        $category = feesCategory::all();
        $university = whereToStudy::all();
        return view('backend.create_online_fee', compact('category', 'university'));
    }

    public function store(Request $request){
        $request->validate([
            'syllabus_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $fees = new onlineFee;
        $fees->degree_id = $request->degree_id;
        $fees->university_id = $request->university_id;
        $fees->type = $request->type;
        $fees->program = $request->program;
        $fees->short_name = $request->short_name;
        $fees->total_fee = $request->total_fee;
        $fees->yearly = $request->yearly;
        $fees->duration = $request->duration;
        $fees->link = $request->link;
        $fees->syllabus_pdf = $this->updateSyllabus($request->file('syllabus_pdf'), null);

        $fees->save();
        $fees->slug = $this->generateUniqueSlug($request->slug ?: $fees->program, $fees->id);
        $fees->save();
        return redirect()->route('fees.online.index')->with('success', 'Cretated Successfully');
    }

    public function edit(Request $request, $id){
        $fee = onlineFee::find($id);
        $category = feesCategory::all();
        $university = whereToStudy::all();
        return view('backend.edit_online_fee', compact('category', 'fee', 'university'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'syllabus_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $fees = onlineFee::find($id);


                $fees->degree_id = $request->degree_id;
                $fees->type = $request->type;
                $fees->program = $request->program;
                $fees->short_name = $request->short_name;
                $fees->total_fee = $request->total_fee;
                $fees->yearly = $request->yearly;
                $fees->duration = $request->duration;
                $fees->university_id = $request->university_id;
                $fees->link = $request->link;
                $fees->syllabus_pdf = $this->updateSyllabus($request->file('syllabus_pdf'), $fees->syllabus_pdf);

                $fees->slug = $this->generateUniqueSlug($request->slug ?: $fees->program, $fees->id);


        $fees->save();

        return redirect()->back()->with('success', 'Updated Successfully');
    }
    
    
   public function recommand($id)
    {
        $onlineFee = OnlineFee::findOrFail($id);
        $onlineFee->recommend = 1;
        $onlineFee->save();

        return redirect()->back()->with('success', 'Recommand Successfully');
    }

    public function not_recommand ($id)
    {
        $onlineFee = OnlineFee::findOrFail($id);
        $onlineFee->recommend = 0;
        $onlineFee->save();

        return redirect()->back()->with('delete', 'Recommand removed');
    }
    

    public function destroy($id)
    {
        $fee = onlineFee::findOrFail($id);

        // Delete associated images
        $this->deleteFileIfExists($fee->syllabus_pdf);

        // Delete the record
        $fee->delete();

        return redirect()->back()->with('success', 'Record deleted successfully');
    }

    private function generateUniqueSlug(?string $program, ?int $ignoreId = null): string
    {
        $base = Str::slug($program ?? '') ?: 'program';
        $slug = $base;
        $counter = 1;

        while (
            onlineFee::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function updateSyllabus($file, ?string $currentPath): ?string
    {
        if (! $file) {
            return $currentPath;
        }

        $this->deleteFileIfExists($currentPath);

        return $this->uploadSyllabus($file);
    }

    private function uploadSyllabus($file): string
    {
        $directory = 'uploads/program-syllabus';
        $destination = public_path($directory);

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $directory . '/' . $filename;
    }

    private function deleteFileIfExists(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}


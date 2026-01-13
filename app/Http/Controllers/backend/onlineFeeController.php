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
            $fees = onlineFee::with('university')
                ->where('status', 1)
                ->latest()
                ->get();

        return view('backend.online_fee', compact('fees'));
    }

    public function export()
    {
        $fees = onlineFee::with(['university', 'feesCategory'])
            ->orderBy('id')
            ->get();

        $columns = [
            'id',
            'program',
            'slug',
            'short_name',
            'university_id',
            'university_name',
            'degree_id',
            'degree_name',
            'type',
            'total_fee',
            'yearly',
            'duration',
            'status',
            'recommend',
            'link',
            'short_description',
            'long_description',
            'syllabus_pdf',
        ];

        $filename = 'online_fees_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($fees, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($fees as $fee) {
                fputcsv($handle, [
                    $fee->id,
                    $fee->program,
                    $fee->slug,
                    $fee->short_name,
                    $fee->university_id,
                    optional($fee->university)->name,
                    $fee->degree_id,
                    optional($fee->feesCategory)->name,
                    $fee->type,
                    $fee->total_fee,
                    $fee->yearly,
                    $fee->duration,
                    $fee->status,
                    $fee->recommend,
                    $fee->link,
                    $fee->short_description,
                    $fee->long_description,
                    $fee->syllabus_pdf,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if (! $handle) {
            return redirect()->back()->with('success', 'Import failed: unable to read the CSV file.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return redirect()->back()->with('success', 'Import failed: the CSV file is empty.');
        }

        $header = array_map([$this, 'normalizeCsvHeader'], $header);

        $degreeLookup = feesCategory::query()->pluck('id', 'name')->all();
        $degreeLookup = array_change_key_case($degreeLookup, CASE_LOWER);
        $universityLookup = whereToStudy::query()->pluck('id', 'name')->all();
        $universityLookup = array_change_key_case($universityLookup, CASE_LOWER);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $row = array_slice(array_pad($row, count($header), null), 0, count($header));
            $data = array_combine($header, $row);

            if (! $data || $this->isEmptyCsvRow($data)) {
                $skipped++;
                continue;
            }

            $id = $this->normalizeCsvValue($data['id'] ?? null);
            $program = $this->normalizeCsvValue($data['program'] ?? ($data['programs'] ?? null));
            $slug = $this->normalizeCsvValue($data['slug'] ?? null);

            $fee = null;
            if ($id) {
                $fee = onlineFee::find($id);
            }
            if (! $fee && $slug) {
                $fee = onlineFee::where('slug', $slug)->first();
            }

            $universityId = $this->normalizeCsvValue($data['university_id'] ?? null);
            $universityName = $this->normalizeCsvValue($data['university_name'] ?? ($data['university'] ?? null));
            if (! $universityId && $universityName) {
                $universityId = $universityLookup[strtolower($universityName)] ?? null;
            }

            if (! $fee && ! $program) {
                $skipped++;
                $errors[] = "Row {$rowNumber} skipped: missing program.";
                continue;
            }

            if (! $fee && $program && $universityId) {
                $fee = onlineFee::where('program', $program)
                    ->where('university_id', $universityId)
                    ->first();
            }

            if (! $fee) {
                $fee = new onlineFee();
            }

            $degreeId = $this->normalizeCsvValue($data['degree_id'] ?? ($data['fees_category_id'] ?? null));
            $degreeName = $this->normalizeCsvValue($data['degree_name'] ?? ($data['degree'] ?? ($data['category'] ?? null)));
            if (! $degreeId && $degreeName) {
                $degreeId = $degreeLookup[strtolower($degreeName)] ?? null;
            }

            $fee->program = $program ?? $fee->program;
            $fee->short_name = $this->normalizeCsvValue($data['short_name'] ?? null) ?? $fee->short_name;
            $fee->type = $this->normalizeCsvValue($data['type'] ?? null) ?? $fee->type;
            $fee->total_fee = $this->normalizeCsvValue($data['total_fee'] ?? null) ?? $fee->total_fee;
            $fee->yearly = $this->normalizeCsvValue($data['yearly'] ?? null) ?? $fee->yearly;
            $fee->duration = $this->normalizeCsvValue($data['duration'] ?? null) ?? $fee->duration;
            $fee->link = $this->normalizeCsvValue($data['link'] ?? null) ?? $fee->link;
            $fee->short_description = $this->normalizeCsvValue($data['short_description'] ?? null) ?? $fee->short_description;
            $fee->long_description = $this->normalizeCsvValue($data['long_description'] ?? null) ?? $fee->long_description;
            $fee->syllabus_pdf = $this->normalizeCsvValue($data['syllabus_pdf'] ?? null) ?? $fee->syllabus_pdf;

            if ($degreeId !== null) {
                $fee->degree_id = $degreeId;
            }
            if ($universityId !== null) {
                $fee->university_id = $universityId;
            }

            $status = $this->normalizeCsvBoolean($data['status'] ?? null);
            if ($status !== null) {
                $fee->status = $status;
            }

            $recommend = $this->normalizeCsvBoolean($data['recommend'] ?? null);
            if ($recommend !== null) {
                $fee->recommend = $recommend;
            }

            if ($slug) {
                $fee->slug = $this->generateUniqueSlug($slug, $fee->id);
            } elseif (! $fee->slug && $fee->program) {
                $fee->slug = $this->generateUniqueSlug($fee->program, $fee->id);
            }

            $isNew = ! $fee->exists;
            $fee->save();

            if ($isNew) {
                $created++;
            } else {
                $updated++;
            }
        }

        fclose($handle);

        $message = "Import complete. Created {$created}, updated {$updated}, skipped {$skipped}.";
        if ($errors) {
            $message .= ' Issues: ' . implode(' ', array_slice($errors, 0, 5));
        }

        return redirect()->back()->with('success', $message);
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

    private function normalizeCsvHeader(?string $value): string
    {
        $value = $value ?? '';
        $value = ltrim($value, "\xEF\xBB\xBF");
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    private function normalizeCsvValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeCsvBoolean($value): ?int
    {
        $value = $this->normalizeCsvValue($value);
        if ($value === null) {
            return null;
        }

        $value = strtolower($value);
        if (in_array($value, ['1', 'true', 'yes', 'active'], true)) {
            return 1;
        }
        if (in_array($value, ['0', 'false', 'no', 'inactive'], true)) {
            return 0;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function isEmptyCsvRow(array $data): bool
    {
        foreach ($data as $value) {
            if ($this->normalizeCsvValue($value) !== null) {
                return false;
            }
        }

        return true;
    }
}


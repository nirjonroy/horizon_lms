<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\siteInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class siteInformationController extends Controller
{
    public function index()
    {
        $siteinfos = siteInformation::all();

        return view('backend.siteinfo', compact('siteinfos'));
    }

    public function edit(int $id)
    {
        $info = siteInformation::findOrFail($id);

        return view('backend.edit_siteInfo', compact('info'));
    }

    public function update(Request $request, int $id)
    {
        $info = siteInformation::findOrFail($id);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'mobile1' => ['nullable', 'string', 'max:255'],
            'mobile2' => ['nullable', 'string', 'max:255'],
            'email1' => ['nullable', 'email', 'max:255'],
            'email2' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadLogo($request->file('logo'), $info->logo);
        } else {
            unset($data['logo']);
        }

        $info->update($data);

        return redirect()
            ->route('admin.siteinfo.index')
            ->with('success', 'Site information updated successfully.');
    }

    private function uploadLogo($file, ?string $existing = null): string
    {
        $directory = 'uploads/website-images';
        $destination = public_path($directory);

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = 'site-logo-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        if ($existing && File::exists(public_path($existing))) {
            File::delete(public_path($existing));
        }

        return $directory . '/' . $filename;
    }
}

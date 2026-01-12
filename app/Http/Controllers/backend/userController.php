<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    
    public function __construct()
    {
        // Ensure the admin guard is used now that admins are stored separately
        $this->middleware('auth:admin');
    }

    
    public function show(){
        // Always edit the currently authenticated admin record
        $user = Auth::guard('admin')->user();
        return view('backend.user_show', compact('user'));
    }
    
    public function update(Request $request, $id){
        $admin = Auth::guard('admin')->user();

        // Prevent editing a different admin record
        abort_unless($admin && (int) $admin->id === (int) $id, 403);

        // Validate the incoming data against the admins table
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the user's information
        $admin->name = $request->input('name');
        $admin->email = $request->input('email');

        // If a new password is provided, hash it and update it
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->input('password'));
        }

        // Save the changes to the database
        $admin->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Admin updated successfully');
    }
}

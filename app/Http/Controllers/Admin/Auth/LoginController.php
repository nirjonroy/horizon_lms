<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{

    


    protected $redirectTo = '/admin/dashboard';  // 👈 important

    protected function authenticated($request, $user)
{
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
}


    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('admin');  // 👈 use admin guard
    }
    public function showLoginForm()
    {
        return view('backend.Auth.Login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
        return redirect()->intended('/admin/dashboard'); // 👈 correct dashboard
    }

    return back()->withErrors([
        'email' => 'Invalid credentials',
    ]);
}


    public function logout(Request $request)
{
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/admin/login');
}

}


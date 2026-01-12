<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $previousUrl = url()->previous();
        $previousPath = $previousUrl ? parse_url($previousUrl, PHP_URL_PATH) : null;

        $isAuthPage = $previousPath && Str::contains($previousPath, ['/login', '/register', '/password']);

        // Remember where the user came from so we can return after login.
        if (!$isAuthPage && $previousPath && $previousUrl !== $request->fullUrl()) {
            $request->session()->put('url.intended', $previousUrl);
            $request->session()->put('login.redirect', $previousUrl);
        } elseif ($isAuthPage) {
            $request->session()->forget('login.redirect');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Prefer explicit redirect captured earlier; otherwise fall back to intended.
        if ($redirectUrl = $request->session()->pull('login.redirect')) {
            return redirect()->to($redirectUrl);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

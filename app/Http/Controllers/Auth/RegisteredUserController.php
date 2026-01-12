<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        //  if (!auth()->user()) {
        //     abort(403, 'Unauthorized action.');
        // }
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Send welcome mail to the user and notify admin.
        $adminEmail = env('ADMIN_NOTIFY_EMAIL', 'imad@thehorizonsunlimited.com');
        try {
            Mail::raw("Hi {$user->name},\n\nWelcome to Horizons Unlimited! Your account has been created with {$user->email}.", function ($message) use ($user) {
                $message->to($user->email)->subject('Welcome to Horizons Unlimited');
            });
        } catch (\Throwable $e) {
            // swallow mail errors
        }

        try {
            Mail::raw("A new user registered.\n\nName: {$user->name}\nEmail: {$user->email}", function ($message) use ($adminEmail) {
                $message->to($adminEmail)->subject('New user registered');
            });
        } catch (\Throwable $e) {
            // swallow mail errors
        }

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

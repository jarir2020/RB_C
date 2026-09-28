<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'loginEmail' => ['required', 'email'],
            'loginPassword' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $credentials['loginEmail'], 'password' => $credentials['loginPassword']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'loginEmail' => 'Those credentials do not match our workspace records.',
            ]);
        }

        $request->session()->regenerate();

        return to_route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}

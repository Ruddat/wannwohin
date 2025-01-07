<?php

namespace App\Http\Controllers\Backend\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            // Wenn der Benutzer bereits eingeloggt ist, weiterleiten zum Dashboard
            return redirect()->route('verwaltung.dashboard');
        }

        // Wenn der Benutzer nicht eingeloggt ist, Login-Formular anzeigen
        return view('backend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('verwaltung.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('verwaltung.login'));
    }
}

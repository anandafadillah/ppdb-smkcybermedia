<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ]);

        $identifier = $request->identifier;

        $credentials = preg_match('/^\d{10}$/', $identifier)
            ? ['nisn' => $identifier, 'password' => $request->password, 'is_active' => true]
            : ['email' => $identifier, 'password' => $request->password, 'is_active' => true];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'panitia' => redirect()->route('panitia.dashboard'),
                default   => redirect()->route('peserta.dashboard'),
            };
        }

        return back()
            ->withErrors(['identifier' => 'NISN/email atau password yang kamu masukkan salah.'])
            ->onlyInput('identifier');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

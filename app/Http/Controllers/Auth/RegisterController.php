<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'nisn'     => ['required', 'regex:/^\d{10}$/', 'unique:users,nisn'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'      => $request->nama,
            'nisn'      => $request->nisn,
            'password'  => Hash::make($request->password),
            'role'      => 'peserta',
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('peserta.dashboard');
    }
}

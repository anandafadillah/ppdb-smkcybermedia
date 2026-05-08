<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('admin.pengguna.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email',
            'nisn'     => 'nullable|digits:10|unique:users,nisn',
            'role'     => 'required|in:admin,panitia,peserta',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'] ?? null,
            'nisn'      => $validated['nisn'] ?? null,
            'role'      => $validated['role'],
            'password'  => $validated['password'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.edit', ['user' => $pengguna]);
    }

    public function update(Request $request, User $pengguna)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'nisn'  => ['nullable', 'digits:10', Rule::unique('users', 'nisn')->ignore($pengguna->id)],
            'role'  => 'required|in:admin,panitia,peserta',
        ]);

        $pengguna->update($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data pengguna diperbarui.');
    }

    public function toggleAktif(User $pengguna)
    {
        $pengguna->update(['is_active' => !$pengguna->is_active]);

        return back()->with('success', 'Status akun diperbarui.');
    }

    public function resetPassword(Request $request, User $pengguna)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $pengguna->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil direset.');
    }

    public function destroy(Request $request, User $pengguna)
    {
        if ($pengguna->id === $request->user()->id) {
            abort(403, 'Tidak dapat menghapus akun sendiri.');
        }

        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Akun dihapus.');
    }
}

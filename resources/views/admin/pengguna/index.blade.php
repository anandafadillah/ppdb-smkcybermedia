@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Pengguna</h2>
        <a href="{{ route('admin.pengguna.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600 transition">
            + Tambah Pengguna
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-xl border border-success-200 bg-success-50 dark:border-success-800 dark:bg-success-500/10 p-4">
        <p class="text-sm text-success-700 dark:text-success-400">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="flex items-center gap-3">
        <select name="role" onchange="this.form.submit()"
            class="h-9 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 text-sm dark:text-white">
            <option value="">Semua Role</option>
            @foreach(['admin' => 'Admin', 'panitia' => 'Panitia', 'peserta' => 'Peserta'] as $val => $label)
            <option value="{{ $val }}" {{ request('role') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold">Email / NISN</th>
                    <th class="px-4 py-3 text-left font-semibold">Role</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td class="px-4 py-3 text-gray-800 dark:text-white font-medium">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $user->email ?? $user->nisn ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $user->role === 'admin' ? 'bg-brand-100 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400' : '' }}
                            {{ $user->role === 'panitia' ? 'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400' : '' }}
                            {{ $user->role === 'peserta' ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' : '' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.pengguna.toggle-aktif', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs font-medium {{ $user->is_active ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.pengguna.edit', $user) }}"
                               class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.pengguna.destroy', $user) }}"
                                  onsubmit="return confirm('Hapus pengguna ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-error-600 hover:text-error-700 dark:text-error-400">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Log Aktivitas</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat login dan logout seluruh pengguna</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.activity-log.index') }}"
          class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 shadow-theme-sm">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Pengguna</label>
                <select name="user_id"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    <option value="">Semua pengguna</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }}
                            ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Event</label>
                <select name="event"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    <option value="">Semua event</option>
                    <option value="login"  @selected(request('event') === 'login')>Login</option>
                    <option value="logout" @selected(request('event') === 'logout')>Logout</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['user_id', 'event', 'tanggal']))
                <a href="{{ route('admin.activity-log.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                    Reset
                </a>
                @endif
            </div>

        </div>
    </form>

    {{-- Tabel --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-theme-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Waktu
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Pengguna
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Role
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Event
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            IP Address
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            User Agent
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($log->user)
                                <p class="font-medium text-gray-800 dark:text-white/90">{{ $log->user->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $log->user->nisn ?? $log->user->email }}
                                </p>
                            @else
                                <span class="text-gray-400 italic">Pengguna dihapus</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if($log->user)
                                @php
                                    $roleColor = match($log->user->role) {
                                        'admin'   => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
                                        'panitia' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                                        default   => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $roleColor }}">
                                    {{ ucfirst($log->user->role) }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if($log->event === 'login')
                                <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-success-500"></span>
                                    Login
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                    Logout
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="truncate text-xs text-gray-500 dark:text-gray-500" title="{{ $log->user_agent }}">
                                {{ $log->user_agent ?? '—' }}
                            </p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">
                            Belum ada log aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-800">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    <p class="mt-3 text-xs text-gray-400">
        Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}
        dari {{ $logs->total() }} entri
    </p>
@endsection

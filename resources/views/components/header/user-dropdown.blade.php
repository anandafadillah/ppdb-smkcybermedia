<div class="relative" x-data="{
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; }
}" @click.away="close()">

    <button class="flex items-center gap-2 text-gray-700 dark:text-gray-400" @click.prevent="toggle()" type="button">
        <span class="flex items-center justify-center w-9 h-9 rounded-full bg-brand-100 dark:bg-brand-900 text-brand-600 dark:text-brand-400 font-semibold text-sm">
            {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
        </span>
        <span class="hidden sm:block text-sm font-medium">{{ Auth::user()?->name }}</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-56 rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark z-50"
        style="display: none;">

        <div class="mb-3 px-1">
            <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ Auth::user()?->name }}</span>
            <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
                @auth
                    {{ Auth::user()->email ?? Auth::user()->nisn }}
                @endauth
            </span>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-800 pt-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full gap-3 px-3 py-2 rounded-lg text-theme-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>

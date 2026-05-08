<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | PPDB SMK Cyber Media Jakarta</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const saved = localStorage.getItem('theme');
                    const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = saved || system;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                isExpanded: window.innerWidth >= 1280,
                isMobileOpen: false,
                isHovered: false,
                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    this.isMobileOpen = false;
                },
                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                },
                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },
                setHovered(val) {
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = saved || system;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark', 'bg-gray-900');
            }
        })();
    </script>

    {{-- Flatpickr: date picker dengan navigasi tahun & bulan --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/airbnb.css">
    <style>
        .flatpickr-input { background-color: transparent; }
        html.dark .flatpickr-calendar { background: #1f2937; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.4); }
        html.dark .flatpickr-calendar .flatpickr-months .flatpickr-month,
        html.dark .flatpickr-calendar .flatpickr-weekdays,
        html.dark .flatpickr-calendar .flatpickr-weekday { background: #1f2937; color: #d1d5db; }
        html.dark .flatpickr-day { color: #e5e7eb; }
        html.dark .flatpickr-day.today { border-color: #465fff; }
        html.dark .flatpickr-day.selected { background: #465fff; border-color: #465fff; color: #fff; }
        html.dark .flatpickr-day:hover { background: #374151; }
        html.dark .flatpickr-day.disabled, html.dark .flatpickr-day.flatpickr-disabled { color: #4b5563; }
        html.dark .flatpickr-current-month .flatpickr-monthDropdown-months,
        html.dark .flatpickr-current-month input.cur-year { color: #fff; }
        html.dark .flatpickr-monthDropdown-months option { background: #1f2937; }
        html.dark .flatpickr-months .flatpickr-prev-month,
        html.dark .flatpickr-months .flatpickr-next-month { color: #d1d5db; fill: #d1d5db; }
    </style>
</head>

<body
    x-data="{ loaded: true }"
    x-init="
        $store.sidebar.isExpanded = window.innerWidth >= 1280;
        const checkMobile = () => {
            if (window.innerWidth < 1280) {
                $store.sidebar.setMobileOpen(false);
                $store.sidebar.isExpanded = false;
            } else {
                $store.sidebar.isMobileOpen = false;
                $store.sidebar.isExpanded = true;
            }
        };
        window.addEventListener('resize', checkMobile);
    ">

    <x-common.preloader />

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            @include('layouts.app-header')

            @if (session('success'))
                <div class="mx-4 mt-4 md:mx-6 rounded-lg bg-success-50 border border-success-200 px-4 py-3 text-sm text-success-700">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="mx-4 mt-4 md:mx-6 rounded-lg bg-warning-50 border border-warning-200 px-4 py-3 text-sm text-warning-700">
                    {{ session('warning') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mx-4 mt-4 md:mx-6 rounded-lg bg-error-50 border border-error-200 px-4 py-3 text-sm text-error-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Flatpickr: auto-init semua input[type=date] dengan navigasi tahun & bulan --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initFlatpickr = (root = document) => {
                root.querySelectorAll('input[type="date"]:not([data-fp-init])').forEach(el => {
                    el.setAttribute('data-fp-init', '1');
                    flatpickr(el, {
                        locale: 'id',
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd F Y',
                        allowInput: true,
                        disableMobile: true,
                    });
                });
            };
            initFlatpickr();
            // Re-init untuk konten dinamis (mis. setelah Alpine x-show / x-if menampilkan field baru)
            const observer = new MutationObserver(muts => {
                muts.forEach(m => m.addedNodes.forEach(n => {
                    if (n.nodeType === 1) initFlatpickr(n);
                }));
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>

    @stack('scripts')
</body>

</html>

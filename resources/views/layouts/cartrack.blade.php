<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}"> --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    @vite('resources/css/app.css')
    @stack('styles')
    <style>
        [x-cloak] {
            display: none;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <header class="bg-primary shadow" x-data="{ isOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo"
                            class="h-8 w-auto sm:h-12 mr-3">
                        <h1 class="text-lg sm:text-xl font-semibold text-white">Monitoring Alat Berat Cimancis</h1>
                    </div>
                    <!-- Hamburger menu button -->
                    <div class="md:hidden">
                        <button @click="isOpen = !isOpen" type="button"
                            class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                            aria-label="Toggle menu">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 fill-current">
                                <path x-show="!isOpen" fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"></path>
                                <path x-show="isOpen" fill-rule="evenodd" clip-rule="evenodd"
                                    d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <!-- Navigation menu -->
                    <nav class="hidden md:flex space-x-4">
                        <a href="{{ route('guest.index') }}"
                            class="text-white  px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('guest.index') ? 'bg-secondary' : '' }}">Home</a>
                        <a href="{{ route('guest.map') }}"
                            class="text-white  px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('guest.map') ? 'bg-secondary' : '' }}">Peta
                            Pekerjaan</a>
                        @if (auth()->check())
                            @if (auth()->user()->hasRole('admin'))
                                <a href="{{ route('dashboard') }}"
                                    class="text-white  px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-secondary' : '' }}">Dashboard</a>
                            @endif

                            @if (auth()->user()->hasRole('operator') || auth()->user()->hasRole('helper'))
                                <a href="{{ route('operator-helper.dashboard') }}"
                                    class="text-white  px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('operator-helper.dashboard') ? 'bg-secondary' : '' }}">Dashboard</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="text-white  block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('login') ? 'bg-secondary' : '' }}">Login</a>
                        @endif
                    </nav>
                </div>
                <!-- Mobile menu, show/hide based on menu state. -->
                <div x-show="isOpen" class="md:hidden" x-cloak>
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                        <a href="{{ route('guest.index') }}"
                            class="text-white  block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('guest.index') ? 'bg-secondary' : '' }}">Home</a>
                        <a href="{{ route('guest.map') }}"
                            class="text-white  block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('guest.map') ? 'bg-secondary' : '' }}">Peta
                            Pekerjaan</a>
                        @if (auth()->check())
                            @if (auth()->user()->hasRole('admin'))
                                <a href="{{ route('dashboard') }}"
                                    class="text-white  block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-secondary' : '' }}">Admin
                                    Dashboard</a>
                            @endif

                            @if (auth()->user()->hasRole('operator') || auth()->user()->hasRole('helper'))
                                <a href="{{ route('operator-helper.dashboard') }}"
                                    class="text-white  block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('operator-helper.dashboard') ? 'bg-secondary' : '' }}">Operator/Helper
                                    Dashboard</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="text-white  block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('login') ? 'bg-secondary' : '' }}">Login</a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1  h-full w-full">
            {{ $slot }}
        </main>
    </div>

    @vite('resources/js/app.js')
    @stack('scripts')
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Operator/Helper</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
    <style>
        [x-cloak] { display: none; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div x-cloak x-data="{ sidebarOpen: false, userMenuOpen: false }" x-init="sidebarOpen = false" class="flex h-screen bg-gray-200">
           <!-- Sidebar -->
           <div :class="{'translate-x-0 ease-out': sidebarOpen, '-translate-x-full ease-in': !sidebarOpen}" class="fixed z-50 inset-y-0 left-0 w-64 transition duration-300 transform bg-primary overflow-y-auto lg:translate-x-0 lg:static lg:inset-0">
                <!-- Sidebar content -->
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo" class="h-20 sm:h-24 w-auto">
                        <span class=" text-white text-md sm:text-lg font-semibold">Monitoring Alat Berat Cimancis</span>
                    </div>
                    <button @click="sidebarOpen = false" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <nav class="mt-8">
                    <!-- Navigation items -->
                    <a class="flex items-center mt-4 py-2 px-6 text-white hover:bg-white hover:bg-opacity-25 hover:text-gray-100 {{ request()->routeIs('operator-helper.dashboard') ? 'bg-white bg-opacity-25 text-gray-100' : '' }}"
                       href="{{ route('operator-helper.dashboard') }}">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="mx-3">Dashboard</span>
                    </a>
                    <a class="flex items-center mt-4 py-2 px-6 text-white hover:bg-white hover:bg-opacity-25 hover:text-gray-100 {{ request()->routeIs('operator-helper.history*') ? 'bg-white bg-opacity-25 text-gray-100' : '' }}"
                        href="{{ route('operator-helper.history') }}">
                         <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                         </svg>
                         <span class="mx-3">Riwayat Pengerjaan</span>
                     </a>
                    <!-- Add more navigation items as needed -->
                </nav>
            </div>

            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center">
                        <!-- User menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                                <div class="py-1 rounded-md bg-white shadow-xs">
                                    <a href="{{ route('operator-helper.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150">
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150">
                                            Log Out
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    @yield('header')
                    <div class="container mx-auto px-6 py-8">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Styles -->
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}"> --}}
    @vite('resources/css/app.css')

    <style>
        [x-cloak] {
            display: none;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div x-cloak x-data="{ sidebarOpen: false, userMenuOpen: false }" x-init="sidebarOpen = false" class="flex h-screen bg-gray-200">
            <!-- Sidebar -->
            <div :class="{ 'translate-x-0 ease-out': sidebarOpen, '-translate-x-full ease-in': !sidebarOpen }"
                class="fixed z-50 inset-y-0 left-0 w-64 transition duration-300 transform bg-primary overflow-y-auto lg:translate-x-0 lg:static lg:inset-0">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo"
                            class="h-20 sm:h-24 w-auto">
                        <span class="text-white sm:text-md text-lg font-semibold">Monitoring Alat Berat Cimancis</span>
                    </div>
                    <button @click="sidebarOpen = false" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <nav class="mt-8">
                    @php
                        $routes = [
                            [
                                'name' => 'Dashboard',
                                'route' => 'dashboard',
                                'pattern' => 'admin',
                                'icon' =>
                                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                            ],
                            [
                                'name' => 'Alat Berat',
                                'route' => 'alat-berat.index',
                                'pattern' => 'admin/alat-berat*',
                                'icon' =>
                                    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            ],
                            [
                                'name' => 'Proyek Pekerjaan',
                                'route' => 'work-assignments.index',
                                'pattern' => 'admin/work-assignments*',
                                'icon' =>
                                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                            ],
                            [
                                'name' => 'Pekerjaan Selesai',
                                'route' => 'completed-projects.index',
                                'pattern' => 'admin/completed-projects*',
                                'icon' =>
                                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                            ],
                            [
                                'name' => 'Peta Proyek',
                                'route' => 'project-map.index',
                                'pattern' => 'admin/project-map*',
                                'icon' =>
                                    'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                            ],
                            [
                                'name' => 'Pegawai',
                                'route' => 'users.index',
                                'pattern' => 'admin/users*',
                                'icon' =>
                                    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                            ],
                            [
                                'name' => 'Laporan',
                                'route' => 'laporan.index',
                                'pattern' => 'admin/laporan*',
                                'icon' =>
                                    'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            ],
                            [
                                'name' => 'Cartrack Vehicles',
                                'route' => 'cartrack-vehicle.index',
                                'pattern' => 'admin/cartrack-vehicle*',
                                'icon' =>
                                    'M3 10h18M3 14h18M5 6h.01M9 6h.01M13 6h.01M17 6h.01M5 18h.01M9 18h.01M13 18h.01M17 18h.01',
                            ],
                            [
                                'name' => 'Cartrack Activity',
                                'route' => 'cartrack-activity.index',
                                'pattern' => 'admin/cartrack-activity*',
                                'icon' =>
                                    'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z',
                            ],
                        ];
                    @endphp

                    @foreach ($routes as $item)
                        <a class="flex items-center mt-4 py-2 px-6 text-white hover:bg-white hover:bg-opacity-25 hover:text-gray-100 {{ request()->is($item['pattern']) ? 'bg-white bg-opacity-25 text-gray-100' : '' }}"
                            href="{{ route($item['route']) }}">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $item['icon'] }}"></path>
                            </svg>

                            <span class="mx-3">{{ $item['name'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center">
                        @include('layouts.navigation')
                    </div>
                </header>

                <!-- Main content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <div class="container mx-auto px-6 py-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </div>
    @vite('resources/js/app.js')
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>

</html>

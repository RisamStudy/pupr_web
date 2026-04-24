@extends('layouts.operator-helper')

@section('header')
    <header class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-2xl text-white leading-tight">
                {{ __('Detail Riwayat Pengerjaan') }} - {{ $workAssignment->project_name }}
            </h2>
        </div>
    </header>
@endsection

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="bg-gradient-to-r from-blue-100 to-indigo-100 p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Informasi Pekerjaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p class="flex items-center"><svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg><span class="font-medium">Nama Proyek:</span> {{ $workAssignment->project_name }}</p>
                    <p class="flex items-center"><svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="font-medium">Tanggal Mulai:</span> {{ $workAssignment->start_date->format('d/m/Y') }}</p>
                    <p class="flex items-center"><svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span class="font-medium">Tanggal Selesai:</span> {{ $workAssignment->end_date->format('d/m/Y') }}</p>
                    <p class="flex items-center"><svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="font-medium">Status:</span> <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $workAssignment->status == 'Sedang Berlangsung' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ $workAssignment->status }}</span></p>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">Riwayat Absensi dan Aktivitas</h3>
            <div class="space-y-4">
                @foreach($attendanceLogs as $date => $logs)
                    <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                        <button @click="open = !open" class="w-full px-4 py-2 text-left font-medium bg-gray-100 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 ease-in-out flex justify-between items-center">
                            <span>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                            <svg class="h-5 w-5 transform transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="px-4 pt-4 pb-2 bg-white">
                            @foreach($logs as $log)
                                <div class="mb-4 p-4 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="space-y-4 sm:space-y-6">
                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                            <div class="flex items-center space-x-2 text-sm sm:text-base">
                                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                                <span class="font-medium">Check In:</span>
                                            </div>
                                            <div class="text-sm sm:text-base">
                                                <span>Jam: {{ $log->check_in_time->format('H:i') }}</span>
                                                <span class="block sm:inline sm:ml-2">Koordinat: {{ $log->check_in_location }}</span>
                                            </div>
                                        </div>

                                        @if($log->check_out_time)
                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                            <div class="flex items-center space-x-2 text-sm sm:text-base">
                                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                <span class="font-medium">Check Out:</span>
                                            </div>
                                            <div class="text-sm sm:text-base">
                                                <span>{{ $log->check_out_time->format('H:i') }}</span>
                                                <span class="block sm:inline sm:ml-2">Koordinat: {{ $log->check_out_location }}</span>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                            <div class="flex items-center space-x-2 text-sm sm:text-base">
                                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="font-medium">Hours Meter Start:</span>
                                            </div>
                                            <span class="text-sm sm:text-base">{{ $log->hours_meter_start }}</span>
                                        </div>

                                        @if($log->hours_meter_end)
                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                            <div class="flex items-center space-x-2 text-sm sm:text-base">
                                                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="font-medium">Hours Meter End:</span>
                                            </div>
                                            <span class="text-sm sm:text-base">{{ $log->hours_meter_end }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <h5 class="text-sm font-semibold mb-2">Foto Absensi:</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            @if($log->check_in_photo)
                                                <div class="relative group">
                                                    <a href="{{ asset($log->check_in_photo) }}" data-lightbox="check-in-{{ $log->id }}" data-title="Foto Check In {{ $log->check_in_time->format('d/m/Y H:i') }}">
                                                        <img src="{{ asset($log->check_in_photo) }}" alt="Check In Photo" class="w-full h-32 object-cover rounded-lg group-hover:opacity-75 transition-opacity duration-300">
                                                        <span class="absolute top-0 right-0 bg-green-500 text-white text-xs px-2 py-1 rounded-bl-lg">Check In</span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if($log->check_out_photo)
                                                <div class="relative group">
                                                    <a href="{{ asset($log->check_out_photo) }}" data-lightbox="check-out-{{ $log->id }}" data-title="Foto Check Out {{ $log->check_out_time->format('d/m/Y H:i') }}">
                                                        <img src="{{ asset($log->check_out_photo) }}" alt="Check Out Photo" class="w-full h-32 object-cover rounded-lg group-hover:opacity-75 transition-opacity duration-300">
                                                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs px-2 py-1 rounded-bl-lg">Check Out</span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if($log->hours_meter_start_photo)
                                                <div class="relative group">
                                                    <a href="{{ asset($log->hours_meter_start_photo) }}" data-lightbox="hours-meter-start-{{ $log->id }}" data-title="Foto Hours Meter Start {{ $log->check_in_time->format('d/m/Y H:i') }}">
                                                        <img src="{{ asset($log->hours_meter_start_photo) }}" alt="Hours Meter Start Photo" class="w-full h-32 object-cover rounded-lg group-hover:opacity-75 transition-opacity duration-300">
                                                        <span class="absolute top-0 right-0 bg-blue-500 text-white text-xs px-2 py-1 rounded-bl-lg">Hours Start</span>
                                                    </a>
                                                </div>
                                            @endif
                                            @if($log->hours_meter_end_photo)
                                                <div class="relative group">
                                                    <a href="{{ asset($log->hours_meter_end_photo) }}" data-lightbox="hours-meter-end-{{ $log->id }}" data-title="Foto Hours Meter End {{ $log->check_out_time->format('d/m/Y H:i') }}">
                                                        <img src="{{ asset($log->hours_meter_end_photo) }}" alt="Hours Meter End Photo" class="w-full h-32 object-cover rounded-lg group-hover:opacity-75 transition-opacity duration-300">
                                                        <span class="absolute top-0 right-0 bg-indigo-500 text-white text-xs px-2 py-1 rounded-bl-lg">Hours End</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(isset($fieldConditionPhotos[$date]))
                                <div class="mt-4">
                                    <h5 class="text-sm font-semibold mb-2">Foto Kondisi Lapangan:</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach($fieldConditionPhotos[$date] as $photo)
                                            <div class="relative group">
                                                <a href="{{ asset($photo->photo_path) }}" data-lightbox="field-condition-{{ $date }}" data-title="Foto Kondisi Lapangan {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}">
                                                    <img src="{{ asset($photo->photo_path) }}" alt="Field Condition" class="w-full h-32 object-cover rounded-lg group-hover:opacity-75 transition-opacity duration-300">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<style>
    [x-cloak] { display: none; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "Gambar %1 dari %2"
        });
    });
</script>
@endpush

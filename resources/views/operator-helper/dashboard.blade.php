@extends('layouts.operator-helper')

@section('header')
    <header class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-2xl text-white leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </header>
@endsection

@section('content')
<div class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md shadow" role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow" role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md shadow" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Daily Attendance Section -->
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="font-bold text-xl mb-4 text-gray-800">Absensi Harian</h3>
                @php
                    $now = now();
                    $dailyAttendance = Auth::user()->attendanceLogs()
                        ->where('log_type', 'attendance')
                        ->whereDate('check_in_time', $now->toDateString())
                        ->first();
                    $currentWorkAssignmentId = $currentAssignment->id ?? null;
                    $canCheckIn = $now->hour < 22;
                @endphp

                @if(!$dailyAttendance && $canCheckIn)
                    <form action="{{ route('operator-helper.daily-check-in') }}" method="POST" id="dailyCheckInForm" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="location" id="locationInput">
                        <input type="hidden" name="work_assignment_id" value="{{ $currentWorkAssignmentId }}">
                        <div>
                            <label for="check_in_photo" class="block text-sm font-medium text-gray-700 mb-2">Foto Absen Masuk</label>
                            <input type="file" id="check_in_photo" name="check_in_photo" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="button" onclick="checkInWithLocation()" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                            Absen Masuk
                        </button>
                    </form>
                @elseif(!$dailyAttendance && !$canCheckIn)
                    <p class="text-red-500 font-semibold">Maaf, waktu absen masuk sudah lewat.</p>
                @elseif($dailyAttendance && !$dailyAttendance->check_out_time)
                    <p class="mb-4 text-green-600 font-semibold">Anda sudah absen masuk pada: {{ $dailyAttendance->check_in_time->format('H:i') }}</p>
                    <form action="{{ route('operator-helper.daily-check-out') }}" method="POST" id="dailyCheckOutForm" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="location" id="checkoutLocationInput">
                        <input type="hidden" name="work_assignment_id" value="{{ $currentWorkAssignmentId }}">
                        <div>
                            <label for="check_out_photo" class="block text-sm font-medium text-gray-700 mb-2">Foto Absen Keluar</label>
                            <input type="file" id="check_out_photo" name="check_out_photo" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                        </div>
                        <button type="button" onclick="checkOutWithLocation()" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                            Absen Keluar
                        </button>
                    </form>
                @else
                    <div class="bg-gray-100 p-4 rounded-md">
                        <p class="font-semibold text-gray-800">Anda sudah melakukan absensi hari ini.</p>
                        <p class="text-green-600">Masuk: {{ $dailyAttendance->check_in_time->format('H:i') }}</p>
                        <p class="text-red-600">Keluar: {{ $dailyAttendance->check_out_time->format('H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Work Assignment and Check-in Section -->
        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left column for current assignment -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <h3 class="font-bold text-xl mb-4 text-gray-800">Penugasan Saat Ini</h3>
                            @if($currentAssignment)
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg shadow-md">
                                    <p class="font-bold text-lg text-indigo-700 mb-2">{{ $currentAssignment->project_name }}</p>
                                    <p class="text-gray-700"><span class="font-semibold">Alat Berat:</span> {{ $currentAssignment->heavyEquipment->nomor_lambung }} - {{ $currentAssignment->heavyEquipment->name }}</p>
                                    <p class="text-gray-700"><span class="font-semibold">Lokasi:</span> {{ $currentAssignment->alamat }}</p>
                                    <p class="text-gray-700"><span class="font-semibold">Status:</span>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                            @if($currentAssignment->status == 'Sedang Berlangsung') bg-green-200 text-green-800
                                            @elseif($currentAssignment->status == 'Belum Dimulai') bg-yellow-200 text-yellow-800
                                            @else bg-gray-200 text-gray-800 @endif">
                                            {{ $currentAssignment->status }}
                                        </span>
                                    </p>
                                    <p class="text-gray-700"><span class="font-semibold">Panjang Penanganan:</span> {{ $currentAssignment->panjang_penanganan }} km</p>

                                    @php
                                        $latestLog = $currentAssignment->attendanceLogs()
                                            ->where('user_id', Auth::id())
                                            ->where('log_type', 'work')
                                            ->whereDate('created_at', now()->toDateString())
                                            ->latest()
                                            ->first();

                                        $hasCheckedInToday = Auth::user()->attendanceLogs()
                                            ->where('log_type', 'attendance')
                                            ->whereDate('check_in_time', now()->toDateString())
                                            ->exists();
                                    @endphp

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @if(!$latestLog || $latestLog->check_out_time)
                                            @if($hasCheckedInToday)
                                                <a href="{{ route('operator-helper.check-in.form', $currentAssignment) }}"
                                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                                                    Check In Pekerjaan
                                                </a>
                                            @else
                                                <span class="bg-blue-300 text-white font-bold py-2 px-4 rounded-md opacity-50 cursor-not-allowed">
                                                    Check In Pekerjaan
                                                </span>
                                            @endif
                                        @else
                                            <a href="{{ route('operator-helper.check-out.form', $currentAssignment) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                                                Check Out Pekerjaan
                                            </a>
                                        @endif

                                         @if($hasCheckedInToday)
                                            <a href="{{ route('field-condition-photos.index', $currentAssignment) }}"
                                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-105">
                                                Foto Kondisi Lapangan
                                            </a>
                                        @else
                                            <span class="bg-green-300 text-white font-bold py-2 px-4 rounded-md opacity-50 cursor-not-allowed">
                                                Foto Kondisi Lapangan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-600 italic">Tidak ada penugasan aktif saat ini.</p>
                            @endif
                        </div>

                        <div>
                            <h3 class="font-bold text-xl mb-4 text-gray-800">Lokasi Penugasan</h3>
                            <div id="map" style="width: 100%; height: 400px;" class="rounded-lg shadow-md border border-gray-200"></div>
                        </div>
                    </div>

                    <!-- Right column for recent activities -->
                    <div>
                        <h3 class="font-bold text-xl mb-4 text-gray-800">Aktivitas Terbaru</h3>
                        <div class="bg-white p-4 rounded-lg shadow-md max-h-96 overflow-y-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($recentActivities as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($activity->log_type == 'attendance')
                                                @if($activity->check_out_time)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Absen Keluar
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Absen Masuk
                                                    </span>
                                                @endif
                                            @else
                                                @if($activity->check_out_time)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Check Out Pekerjaan
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Check In Pekerjaan
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">
                                            Tidak ada aktivitas terbaru.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .file-input-wrapper:hover::before {
        border-color: #4a5568;
    }
    .activity-list::-webkit-scrollbar {
        width: 8px;
    }
    .activity-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .activity-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .activity-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    var openGoogleMaps;

    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([-6.7, 108.5], 10);
        
        var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

        });

        var satelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {

        });

        var baseMaps = {
            "Default": osmLayer,
            "Satellite": satelliteLayer
        };

        osmLayer.addTo(map);

        L.control.layers(baseMaps).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; PU Cimancis'
        }).addTo(map);

        @if($currentAssignment && $currentAssignment->latitude && $currentAssignment->longitude)
            var lat = {{ $currentAssignment->latitude }};
            var lng = {{ $currentAssignment->longitude }};

            openGoogleMaps = function() {
                var url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                window.open(url, '_blank');
            };

            var popupContent = `
                <div class="min-w-[150px] max-w-[200px]">
                    <h3 class="font-bold text-md mb-2">{{ $currentAssignment->project_name }}</h3>
                    <p class="mb-3 text-sm break-words" style="margin-top:0 !important;">{{ $currentAssignment->alamat }}</p>
                    <button onclick="openGoogleMaps()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded text-xs transition duration-300 ease-in-out">
                        Lihat di Google Maps
                    </button>
                </div>
            `;

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(popupContent, { maxWidth: 300 })
                .openPopup();

            map.setView([lat, lng], 13);
        @endif
    });

    function setLocation(inputElement) {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                inputElement.value = lat + "," + lon;
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function checkInWithLocation() {
        getLocationAndSubmitForm('locationInput', 'dailyCheckInForm');
    }

    function checkOutWithLocation() {
        getLocationAndSubmitForm('checkoutLocationInput', 'dailyCheckOutForm');
    }

    function getLocationAndSubmitForm(inputId, formId) {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                document.getElementById(inputId).value = lat + "," + lon;
                document.getElementById(formId).submit();
            }, function(error) {
                console.error("Error getting location: ", error);
                handleLocationError(inputId, formId);
            });
        } else {
            console.error("Geolocation is not supported by this browser.");
            handleLocationError(inputId, formId);
        }
    }

    function handleLocationError(inputId, formId) {
        if (confirm("Tidak dapat mengambil lokasi. Apakah Anda ingin melanjutkan absen tanpa lokasi?")) {
            document.getElementById(inputId).value = "Lokasi tidak tersedia";
            document.getElementById(formId).submit();
        }
    }
</script>
@endpush

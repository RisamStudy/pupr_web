<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Alat Berat') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, showHoursMeterModal: false, equipmentToDelete: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                            <dl class="mt-2 border-t border-b border-gray-100">
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Nomor Lambung</dt>
                                    <dd class="text-sm text-gray-900">{{ $heavyEquipment->nomor_lambung }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Nama Kendaraan</dt>
                                    <dd class="text-sm text-gray-900">{{ $heavyEquipment->name }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Tahun</dt>
                                    <dd class="text-sm text-gray-900">{{ $heavyEquipment->tahun }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Merek</dt>
                                    <dd class="text-sm text-gray-900">{{ $heavyEquipment->merek }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Status Alat Berat</dt>
                                    <dd class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $heavyEquipment->status)) }}</dd>
                                </div>
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Kondisi</dt>
                                    <dd class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $heavyEquipment->kondisi)) }}</dd>
                                </div>
                                <div class="py-3 flex justify-between items-center">
                                    <dt class="text-sm font-medium text-gray-500">Hours Meter</dt>
                                    <dd class="text-sm">
                                        <button @click="showHoursMeterModal = true"
                                                class="text-blue-600 hover:text-blue-800 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md">
                                            {{ $heavyEquipment->hours_meter ?? '-' }}
                                            <span class="text-xs text-gray-500">(Lihat riwayat)</span>
                                        </button>
                                    </dd>
                                </div>
                            </dl>
                            <div class="mt-4">
                                <div class="bg-gray-100 shadow-md p-4 rounded-lg">
                                    <h3 class="font-semibold mb-2">Riwayat Hours Meter</h3>
                                    <div class="flex flex-col items-center">
                                        <div class="w-full h-72 mb-4">
                                            <canvas id="equipmentUsageChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informasi Lokasi</h3>
                            <dl class="mt-2 border-t border-b border-gray-100">
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500 mr-2">Lokasi</dt>
                                    <dd class="text-sm text-gray-900">{{ $heavyEquipment->location }}</dd>
                                </div>
                            </dl>
                            @if ($heavyEquipment->current_latitude && $heavyEquipment->current_longitude)
                                <h4 class="mt-4 text-md font-medium text-gray-900">Lokasi Saat Ini</h4>
                                <div id="current-map" class="z-10 mt-2" style="height: 200px;"></div>
                            @endif
                            <h4 class="mt-6 text-md font-medium text-gray-900">Tracking Lokasi</h4>
                            <div id="tracking-map" class="z-10 mt-2" style="height: 300px;"></div>

                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900">Keterangan Titik Tracking</h4>
                                <div class="overflow-y-auto mt-2 max-h-[200px]">
                                    <table class="min-w-full divide-y divide-gray-200 border">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tracking-table-body" class="bg-white divide-y divide-gray-200">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Riwayat Pengerjaan</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Proyek</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($heavyEquipment->workAssignments as $assignment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $assignment->project_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $assignment->city->name }}, {{ $assignment->district->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $assignment->start_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $assignment->end_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $now = now();
                                                    if ($now < $assignment->start_date) {
                                                        $status = 'Belum Dimulai';
                                                        $statusClass = 'text-yellow-600';
                                                    } elseif ($now <= $assignment->end_date) {
                                                        $status = 'Sedang Berlangsung';
                                                        $statusClass = 'text-green-600';
                                                    } else {
                                                        $status = 'Selesai';
                                                        $statusClass = 'text-blue-600';
                                                    }
                                                @endphp
                                                <span class="{{ $statusClass }} font-semibold">{{ $status }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                Tidak ada riwayat pengerjaan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('alat-berat.edit', $heavyEquipment) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Data
                        </a>
                        <button @click="showDeleteModal = true; equipmentToDelete = {{ $heavyEquipment->id }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Hapus Data</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <x-delete-modal show="showDeleteModal">
            <x-slot name="content">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Delete Data
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this data? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <form :action="'{{ route('alat-berat.destroy', '') }}/' + equipmentToDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button @click="showDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </x-slot>
        </x-delete-modal>
        <!-- Hours Meter History Modal -->
        <div x-show="showHoursMeterModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showHoursMeterModal"
                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        @click="showHoursMeterModal = false"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                </div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Riwayat Hours Meter
                                </h3>
                                <div class="mt-2 max-h-[600px] overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Meter Awal</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Meter Akhir</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($hoursMeterHistory as $history)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $history['date'] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $history['start_meter'] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $history['end_meter'] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $history['project_name'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="showHoursMeterModal = false">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <style>
            .tracking-marker {
                background: none;
                border: none;
            }
            .custom-marker {
                background: none;
                border: none;
                transition: all 0.3s ease;
            }
            .marker-container {
                position: relative;
                width: 24px;
                height: 24px;
                transform-origin: center;
                transition: all 0.3s ease;
            }
            .marker-point {
                box-shadow: 0 3px 6px rgba(0,0,0,0.16);
                border: 2px solid white;
                transform: scale(1);
                transition: all 0.3s ease;
                animation: pulse 2s infinite;
            }
            .marker-point:hover {
                transform: scale(1.2);
                box-shadow: 0 5px 12px rgba(0,0,0,0.25);
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
                }
                70% {
                    box-shadow: 0 0 0 6px rgba(37, 99, 235, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
                }
            }

            .tracking-table {
                max-height: 200px;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: #2563EB #EDF2F7;
            }

            .tracking-table::-webkit-scrollbar {
                width: 6px;
            }

            .tracking-table::-webkit-scrollbar-track {
                background: #EDF2F7;
                border-radius: 3px;
            }

            .tracking-table::-webkit-scrollbar-thumb {
                background-color: #2563EB;
                border-radius: 3px;
            }

            .tracking-row {
                transition: all 0.2s ease;
            }

            .tracking-row:hover {
                background-color: #F3F4F6;
                transform: translateX(4px);
            }

            /* Style untuk popup */
            .leaflet-popup-content-wrapper {
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .leaflet-popup-content {
                margin: 0;
                padding: 12px;
            }

            .popup-content {
                background: white;
                border-radius: 8px;
                overflow: hidden;
            }

            .popup-header {
                background: #2563EB;
                color: white;
                padding: 8px 12px;
                margin: 0 -12px 8px -12px;
                font-weight: 600;
            }

            .popup-body {
                padding: 0 4px;
            }

            .popup-info {
                margin: 4px 0;
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.875rem;
            }

            .popup-icon {
                width: 16px;
                height: 16px;
                opacity: 0.7;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Set default center coordinates untuk Cirebon
                const defaultLat = -6.7320229;
                const defaultLng = 108.5523164;

                // Inisialisasi tracking map
                function initializeTrackingMap() {
                    const mapContainer = document.getElementById('tracking-map');
                    if (!mapContainer) {
                        return null;
                    }

                    const trackingMap = L.map('tracking-map', {
                        center: [{{ $heavyEquipment->current_latitude ?? 'defaultLat' }}, {{ $heavyEquipment->current_longitude ?? 'defaultLng' }}],
                        zoom: 13
                    });

                    // Layer peta biasa (OpenStreetMap)
                    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; PU Cimancis'
                    }).addTo(trackingMap);

                    // Layer satellite
                    const satelliteLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                        maxZoom: 30,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    // Layer control
                    const baseMaps = {
                        "Map": osmLayer,
                        "Satellite": satelliteLayer
                    };

                    L.control.layers(baseMaps).addTo(trackingMap);
                    return trackingMap;
                }

                // Inisialisasi current location map jika ada koordinat
                function initializeCurrentMap() {
                    @if ($heavyEquipment->current_latitude && $heavyEquipment->current_longitude)
                        const currentMapContainer = document.getElementById('current-map');
                        if (!currentMapContainer) {
                            return;
                        }

                        const currentMap = L.map('current-map', {
                            center: [{{ $heavyEquipment->current_latitude }}, {{ $heavyEquipment->current_longitude }}],
                            zoom: 15
                        });

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; PU Cimancis'
                        }).addTo(currentMap);

                        L.marker([{{ $heavyEquipment->current_latitude }}, {{ $heavyEquipment->current_longitude }}])
                            .bindPopup('Lokasi Saat Ini')
                            .addTo(currentMap);
                    @endif
                }

                // Fungsi untuk memproses dan menampilkan data tracking
                function processTrackingData(trackingMap, data) {
                    const tableBody = document.getElementById('tracking-table-body');
                    if (!tableBody) {
                        return;
                    }

                    tableBody.innerHTML = '';

                    if (data && data.length > 0) {
                        // Buat path koordinat
                        const pathCoords = data.map(point => [point.latitude, point.longitude]);

                        // Object untuk melacak koordinat yang sudah digunakan
                        const usedCoordinates = {};

                        // Gambar garis tracking
                        const path = L.polyline(pathCoords, {
                            color: '#2563EB',
                            weight: 3,
                            opacity: 0.8
                        }).addTo(trackingMap);

                        // Offset untuk menggeser marker (dalam derajat)
                        const offset = 0.0001;

                        // Tambah marker untuk setiap titik
                        data.forEach((point, index) => {
                            // Tambah baris pada tabel
                            const row = document.createElement('tr');
                            row.className = 'tracking-row';
                            row.innerHTML = `
                                <td class="px-4 py-3 text-sm text-blue-600 font-semibold">${index + 1}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">${point.date}</td>
                            `;
                            tableBody.appendChild(row);

                            // Koordinat marker
                            let coordKey = `${point.latitude},${point.longitude}`;
                            let adjustedLat = point.latitude;
                            let adjustedLng = point.longitude;

                            if (usedCoordinates[coordKey]) {
                                let angle = (usedCoordinates[coordKey] * Math.PI * 2) / 8;
                                adjustedLat = point.latitude + (offset * Math.cos(angle));
                                adjustedLng = point.longitude + (offset * Math.sin(angle));
                                usedCoordinates[coordKey]++;
                            } else {
                                usedCoordinates[coordKey] = 1;
                            }

                            // Custom marker
                            const customIcon = L.divIcon({
                                html: `
                                    <div class="marker-container">
                                        <div class="marker-point bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-lg">
                                            ${index + 1}
                                        </div>
                                    </div>`,
                                className: 'custom-marker',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            });

                            const trackMarker = L.marker([adjustedLat, adjustedLng], {
                                icon: customIcon
                            }).addTo(trackingMap);

                            // Popup info
                            trackMarker.bindPopup(`
                                <div class="popup-content">
                                    <div class="popup-header">
                                        Point ${index + 1}
                                    </div>
                                    <div class="popup-body">
                                        <div class="popup-info">
                                            <svg class="popup-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>${point.date}</span>
                                        </div>
                                        <div class="popup-info">
                                            <svg class="popup-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>${point.time}</span>
                                        </div>
                                        <div class="popup-info">
                                            <svg class="popup-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <span>Hours Meter: ${point.hours_meter ?? '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });

                        // Fit map ke semua titik
                        if (pathCoords.length > 0) {
                            trackingMap.fitBounds(path.getBounds(), { padding: [50, 50] });
                        }
                    } else {
                        tableBody.innerHTML = `
                            <tr class="tracking-row">
                                <td colspan="2" class="px-4 py-4 text-sm text-gray-500 text-center">
                                    <div class="flex flex-col items-center justify-center py-4">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Belum ada riwayat tracking</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        trackingMap.setView([defaultLat, defaultLng], 10);
                    }
                }

                // Main execution
                try {
                    // Initialize maps
                    const trackingMap = initializeTrackingMap();
                    if (!trackingMap) {
                        throw new Error("Failed to initialize tracking map");
                    }
                    initializeCurrentMap();

                    // Fetch tracking data
                    fetch(`/api/equipment-tracking/{{ $heavyEquipment->id }}`)
                        .then(response => response.json())
                        .then(data => processTrackingData(trackingMap, data))
                        .catch(error => {
                            const tableBody = document.getElementById('tracking-table-body');
                            if (tableBody) {
                                tableBody.innerHTML = `
                                    <tr class="tracking-row">
                                        <td colspan="2" class="px-4 py-4 text-sm text-red-500 text-center">
                                            <div class="flex flex-col items-center justify-center py-4">
                                                <svg class="h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="mt-2 text-sm">Gagal memuat data tracking</p>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }
                        });
                } catch (error) {
                    console.error('Error in main execution:', error);
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Setup data untuk chart dari PHP ke JavaScript
                const hoursMeterData = @json($hoursMeterHistory);

                // Grafik garis
                fetch(`/api/hours-meter-history/{{ $heavyEquipment->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        const ctx = document.getElementById('equipmentUsageChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.map(item => item.week),
                                datasets: [{
                                    label: 'Hours Meter',
                                    data: data.map(item => item.hours_meter),
                                    borderColor: '#2563EB',
                                    backgroundColor: '#2563EB20',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#2563EB',
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            display: true,
                                            color: '#E5E7EB'
                                        },
                                        title: {
                                            display: true,
                                            text: 'Hours Meter'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45,
                                            autoSkip: false
                                        },
                                        grid: {
                                            display: false
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        padding: 12,
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return `Hours Meter: ${context.parsed.y.toFixed(2)}`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                );
            });
        </script>
    @endpush
</x-app-layout>

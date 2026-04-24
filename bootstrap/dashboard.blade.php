<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-4">
                    <label for="year" class="font-medium text-gray-700">Filter Tahun:</label>
                    <select id="year" name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Left column for maps -->
                        <div class="lg:col-span-2">
                            <div class="mb-4">
                                <div id="map" style="width: 100%; height: 600px;" class="rounded-lg border z-10 border-gray-300"></div>
                            </div>

                            <div class="mt-4">
                                <h3 class="font-semibold mb-2">Daftar Alat Berat</h3>
                                <div class="bg-gray-100 p-4 shadow-md rounded-lg max-h-64 overflow-y-auto">
                                    <table class="table-auto w-full">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left">Nomor Lambung</th>
                                                <th class="px-4 py-2 text-left">Nama Alat Berat</th>
                                                <th class="px-4 py-2 text-left">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($heavyEquipments as $equipment)
                                            <tr class="border-t border-gray-200">
                                                <td class="px-4 py-2">{{ $equipment->nomor_lambung }}</td>
                                                <td class="px-4 py-2">{{ $equipment->name }}</td>
                                                <td class="px-4 py-2">
                                                    <span class="rounded-full text-md font-semibold px-2 py-1
                                                        {{ $equipment->status == 'ready'
                                                            ? 'bg-green-200 text-green-800'
                                                            : ($equipment->status == 'beroperasi'
                                                                ? 'bg-blue-200 text-blue-800'
                                                                : ($equipment->status == 'tidak ada'
                                                                ? 'bg-gray-200 text-gray-800'
                                                                : ($equipment->status == 'maintenance'
                                                                    ? 'bg-orange-200 text-orange-800'
                                                                    : ($equipment->status == 'rusak'
                                                                        ? 'bg-red-200 text-red-800'
                                                                        : '')))) }}">
                                                        {{ ucfirst($equipment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Statistics -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                <a href="{{ route('work-assignments.index', ['status' => 'Sedang Berlangsung']) }}" class="block">
                                    <div class="bg-red-100 p-2 rounded-lg text-center aspect-square flex flex-col justify-center">
                                        <h5 class="text-xs font-semibold">Pekerjaan Berjalan</h5>
                                        <p class="text-lg font-bold">{{ $activeProjects }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('completed-projects.index') }}" class="block">
                                    <div class="bg-purple-100 p-2 rounded-lg text-center aspect-square flex flex-col justify-center">
                                        <h5 class="text-xs font-semibold">Pekerjaan Selesai</h5>
                                        <p class="text-lg font-bold">{{ $completedProjects }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('alat-berat.index') }}" class="block">
                                    <div class="bg-yellow-100 p-2 rounded-lg text-center aspect-square flex flex-col justify-center">
                                        <h5 class="text-xs font-semibold">Total Alat Berat</h5>
                                        <p class="text-lg font-bold">{{ $totalHeavyEquipments }}</p>
                                    </div>
                                </a>
                                <a href="{{ route('alat-berat.index', ['status' => 'ready']) }}" class="block">
                                    <div class="bg-green-100 p-2 rounded-lg text-center aspect-square flex flex-col justify-center">
                                        <h5 class="text-xs font-semibold">Alat Berat Tersedia</h5>
                                        <p class="text-lg font-bold">{{ $availableEquipments }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg mt-4">
                                <h3 class="font-semibold mb-2">Riwayat Penggunaan Alat Berat</h3>
                                <div class="flex flex-col items-center">
                                    <div class="w-full h-72 mb-4">
                                        <canvas id="equipmentUsageChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right column for project list and search -->
                        <div>
                            <div class="bg-gray-100 p-6 shadow-md rounded-lg mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-semibold text-lg">Pekerjaan</h3>
                                    <span class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">
                                        On Duty
                                    </span>
                                </div>

                                <div class="max-h-[255px] overflow-y-auto">
                                    <table class="min-w-full bg-white rounded-lg overflow-hidden sticky top-0">
                                        <thead class="bg-gray-50 border-b sticky top-0 z-10">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                                    Alat Berat
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                                    Operator
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                                    Helper
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($ongoingProjects as $project)
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <span class="h-2 w-2 bg-green-400 rounded-full mr-2"></span>
                                                        <span class="text-sm font-medium text-gray-900">
                                                            {{ $project->heavyEquipment->nomor_lambung }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="space-y-1">
                                                        @foreach($project->assignmentUsers->where('role', 'operator') as $operator)
                                                            <div class="flex items-center">
                                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                                    <span class="text-sm font-medium text-blue-800">
                                                                        {{ substr($operator->user->name, 0, 1) }}
                                                                    </span>
                                                                </div>
                                                                <span class="ml-3 text-sm text-gray-700">
                                                                    {{ $operator->user->name }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="space-y-1">
                                                        @foreach($project->assignmentUsers->where('role', 'helper') as $helper)
                                                            <div class="flex items-center">
                                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                                    <span class="text-sm font-medium text-purple-800">
                                                                        {{ substr($helper->user->name, 0, 1) }}
                                                                    </span>
                                                                </div>
                                                                <span class="ml-3 text-sm text-gray-700">
                                                                    {{ $helper->user->name }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 text-center text-gray-500 text-sm">
                                                    Tidak ada pekerjaan yang sedang berlangsung
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Chart section -->
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg">
                                <h3 class="font-semibold mb-2">Status Alat Berat</h3>
                                <div class="flex flex-col items-center">
                                    <div class="w-full h-64 mb-4">
                                        <canvas id="equipmentStatusChart"></canvas>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-semibold">Total Alat Berat: {{ $heavyEquipments->count() }}</p>
                                        <p>Ready: {{ $heavyEquipments->where('status', 'ready')->count() }} ({{ number_format($heavyEquipments->where('status', 'ready')->count() / $heavyEquipments->count() * 100, 1) }}%)</p>
                                        <p>Beroperasi: {{ $heavyEquipments->where('status', 'beroperasi')->count() }} ({{ number_format($heavyEquipments->where('status', 'beroperasi')->count() / $heavyEquipments->count() * 100, 1) }}%)</p>
                                        <p>Maintenance: {{ $heavyEquipments->where('status', 'maintenance')->count() }} ({{ number_format($heavyEquipments->where('status', 'maintenance')->count() / $heavyEquipments->count() * 100, 1) }}%)</p>
                                        <p>Rusak: {{ $heavyEquipments->where('status', 'rusak')->count() }} ({{ number_format($heavyEquipments->where('status', 'rusak')->count() / $heavyEquipments->count() * 100, 1) }}%)</p>
                                        <p>Tidak ada: {{ $heavyEquipments->where('status', 'tidak ada')->count() }} ({{ number_format($heavyEquipments->where('status', 'tidak ada')->count() / $heavyEquipments->count() * 100, 1) }}%)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-100 shadow-md p-4 rounded-lg mt-4">
                                <h3 class="font-semibold mb-2">Aktivitas Terbaru</h3>
                                <div class="max-h-64 overflow-y-auto">
                                    <div class="space-y-3">
                                        @foreach($recentActivities as $activity)
                                            <div class="flex items-center bg-white p-3 rounded-lg shadow-sm">
                                                <div class="flex-shrink-0 w-2 h-2 {{
                                                    $activity->type == 'start' ? 'bg-green-500' :
                                                    ($activity->type == 'end' ? 'bg-red-500' : 'bg-blue-500')
                                                }} rounded-full mr-3"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $activity->project_name }}
                                                    </p>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <p class="text-xs text-gray-500">
                                                            {{ $activity->description }}
                                                        </p>
                                                        <span class="text-xs text-gray-400">
                                                            {{ $activity->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
        <style>
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 3px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
            #map{
                height: 350px !important;
            }
             @media (max-width: 768px) {
                .leaflet-popup-content-wrapper {
                    max-width: 220px;
                }
                .leaflet-popup-content {
                    margin: 10px;
                    font-size: 12px;
                }
                .mobile-popup-content {
                    flex-direction: column;
                }
                .mobile-popup-content .popup-image {
                    width: 100%;
                    height: auto;
                    margin-top: 10px;
                }
            }
            .leaflet-control a {
                background-color: #fff;
                border-bottom: 1px solid #ccc;
                width: 26px;
                height: 26px;
                line-height: 26px;
                display: block;
                text-align: center;
                text-decoration: none;
                color: black;
            }
            .leaflet-control a:hover {
                background-color: #f4f4f4;
            }
            .leaflet-control a:first-child {
                border-top-left-radius: 4px;
                border-top-right-radius: 4px;
            }
            .leaflet-control a:last-child {
                border-bottom-left-radius: 4px;
                border-bottom-right-radius: 4px;
                border-bottom: none;
            }
            .project-popup {
                font-family: Arial, sans-serif;
                display: flex;
                max-width: 280px;
            }

            .project-info {
                flex: 1;
                padding-right: 10px;
            }

            .project-title {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 5px;
                color: #333 !important;
            }

            .project-details p {
                font-size: 11px;
                margin: 0 0 2px 0 !important;
                color: #666 !important;
            }

            .project-links {
                display: flex;
                flex-direction: column;
                margin-top: 5px;
            }

            .project-links a {
                text-decoration: none;
                color: #fff !important;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 11px;
                margin-bottom: 3px;
                text-align: center;
                transition: background-color 0.3s;
            }

            .doc-link {
                background-color: #030F6B !important;
            }

            .doc-link:hover {
                background-color: #fd7e14 !important;
            }

            .direction-link {
                background-color: #38a169 !important;
            }

            .direction-link:hover {
                background-color: #2f855a !important;
            }

            .project-image-container {
                width: 125px;
                height: 125px;
                overflow: hidden;
                border-radius: 3px;
            }

            .project-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .no-image {
                background-color: #f1f1f1;
                color: #999;
                text-align: center;
                padding: 10px;
                border-radius: 3px;
                font-size: 11px;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .leaflet-popup-content-wrapper {
                padding: 0;
            }

            .leaflet-popup-content {
                margin: 0;
                padding: 10px;
            }

            /* Responsive styles for mobile */
            @media (max-width: 480px) {
                .leaflet-popup-content {
                    max-width: 250px;
                }
                .project-popup {
                    max-width: 240px;
                }

                .project-title {
                    font-size: 13px;
                }

                .project-details p {
                    font-size: 10px;
                }

                .project-links a {
                    font-size: 10px;
                    padding: 3px 6px;
                }

                .project-image-container {
                    width: 100px;
                    height: 100px;
                }
            }
            .user-location-icon {
                font-size: 30px;
                text-align: center;
                line-height: 40px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadEquipmentUsageChart();
            document.getElementById('year').addEventListener('change', function() {
                loadEquipmentUsageChart();
            });
            var map = L.map('map', {
                center: [-6.7, 108.5],
                zoom: 10,
                dragging: true,
                touchZoom: true,
                scrollWheelZoom: true,
                doubleClickZoom: false,
                boxZoom: false,
                tap: false,
                keyboard: false,
                zoomControl: true
            });

            // Define base layers
            var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            });

            var satelliteLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 30,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            });

            // Set default layer
            osmLayer.addTo(map);

            var completedIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var ongoingIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; PU Cimancis'
            }).addTo(map);

            var completedProjectsLayer = L.layerGroup().addTo(map);
            var ongoingProjectsLayer = L.layerGroup().addTo(map);

            function createPopupContent(project) {
                return `
                    <div class="project-popup">
                        <div class="project-info">
                            <h3 class="project-title">${project.project_name}</h3>
                            <div class="project-details">
                                <p>${project.village_name}, ${project.district_name}</p>
                                <p>${project.city_name}</p>
                            </div>
                            <div class="project-links">
                                ${project.documentation_link ?
                                `<a href="${project.documentation_link}" target="_blank" class="doc-link">Dokumentasi</a>` :
                                ''}
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${project.latitude},${project.longitude}" target="_blank" class="direction-link">Menuju Lokasi</a>
                            </div>
                        </div>
                        <div class="project-image-container">
                            ${project.image_url ?
                                `<a href="${project.image_url}" data-fancybox="gallery" data-caption="${project.project_name}" class="project-image">
                                    <img src="${project.image_url}" alt="${project.project_name}">
                                </a>` :
                                `<div class="no-image">Tidak ada gambar</div>`
                            }
                        </div>
                    </div>
                `;
            }

            @foreach($completedProjectsMap as $project)
                @if($project->latitude && $project->longitude)
                    L.marker([{{ $project->latitude }}, {{ $project->longitude }}], {icon: completedIcon})
                    .addTo(completedProjectsLayer)
                    .bindPopup(createPopupContent({
                        project_name: "{{ $project->project_name }}",
                        village_name: "{{ $project->village->name ?? 'N/A' }}",
                        district_name: "{{ $project->district->name ?? 'N/A' }}",
                        city_name: "{{ $project->city->name ?? 'N/A' }}",
                        documentation_link: "{{ $project->documentation_link ?? '' }}",
                        latitude: "{{ $project->latitude }}",
                        longitude: "{{ $project->longitude }}",
                        image_url: "{{ $project->fieldConditionPhotos->first() ? asset($project->fieldConditionPhotos->first()->photo_path) : '' }}"
                    }));
                @endif
            @endforeach

            @foreach($ongoingProjectsMap as $project)
                @if($project->latitude && $project->longitude)
                    L.marker([{{ $project->latitude }}, {{ $project->longitude }}], {icon: ongoingIcon})
                    .addTo(ongoingProjectsLayer)
                    .bindPopup(createPopupContent({
                        project_name: "{{ $project->project_name }}",
                        village_name: "{{ $project->village->name ?? 'N/A' }}",
                        district_name: "{{ $project->district->name ?? 'N/A' }}",
                        city_name: "{{ $project->city->name ?? 'N/A' }}",
                        documentation_link: "{{ $project->documentation_link ?? '' }}",
                        latitude: "{{ $project->latitude }}",
                        longitude: "{{ $project->longitude }}",
                        image_url: "{{ $project->fieldConditionPhotos->first() ? asset($project->fieldConditionPhotos->first()->photo_path) : '' }}"
                    }));
                @endif
            @endforeach


            // Define base maps and overlay maps
            var baseMaps = {
                "Default": osmLayer,
                "Satellite": satelliteLayer
            };

            var overlayMaps = {
                "Proyek Selesai": completedProjectsLayer,
                "Proyek Sedang Berjalan": ongoingProjectsLayer
            };

            L.control.layers(baseMaps, overlayMaps).addTo(map);

            // Custom Legend
            var legend = L.control({position: 'bottomright'});

            legend.onAdd = function (map) {
                var div = L.DomUtil.create('div', 'info legend');
                div.innerHTML += '<div class="p-2 bg-white rounded shadow">' +
                                '<h4 class="font-semibold mb-2">Status</h4>' +
                                '<div class="flex items-center mb-1"><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png" alt="" style="width: 12px; height: 20px;"><span class="ml-2">Proyek Selesai</span></div>' +
                                '<div class="flex items-center"><img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" alt="" style="width: 12px; height: 20px;"><span class="ml-2">Proyek Sedang Berjalan</span></div>' +
                                '</div>';
                return div;
            };

            legend.addTo(map);

            function adjustMapHeight() {
                var mapElement = document.getElementById('map');
                var windowHeight = window.innerHeight;
                var mapHeight = windowHeight * 0.4; // 40% dari tinggi layar
                mapElement.style.height = mapHeight + 'px';
                map.invalidateSize();
            }

            // Panggil fungsi saat halaman dimuat dan saat ukuran window berubah
            adjustMapHeight();
            window.addEventListener('resize', adjustMapHeight);

            // Initialize Fancybox
            Fancybox.bind("[data-fancybox]", {
                // Fancybox options here if needed
            });

            // Re-initialize Fancybox when a popup is opened
            map.on('popupopen', function(e) {
                Fancybox.bind(e.popup._contentNode.querySelectorAll("[data-fancybox]"), {
                    // Fancybox options here if needed
                });
            });

            function adjustMapHeight() {
                var mapElement = document.getElementById('map');
                var windowHeight = window.innerHeight;
                var mapHeight = windowHeight * 0.4; // 40% dari tinggi layar
                mapElement.style.height = mapHeight + 'px';
                map.invalidateSize();
            }

            adjustMapHeight();
            window.addEventListener('resize', adjustMapHeight);

            var userLocationMarker;

            function addUserLocation() {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lon = position.coords.longitude;

                        if (userLocationMarker) {
                            map.removeLayer(userLocationMarker);
                        }

                        var userIcon = L.divIcon({
                            html: '📍',
                            iconSize: [30, 30],
                            iconAnchor: [20, 40],
                            popupAnchor: [0, -40],
                            className: 'user-location-icon'
                        });

                        userLocationMarker = L.marker([lat, lon], {icon: userIcon}).addTo(map);
                        userLocationMarker.bindPopup("Lokasi Anda").openPopup();

                        map.setView([lat, lon], 10);
                    }, function(error) {
                        console.error("Error getting user location:", error);
                    });
                } else {
                    console.error("Geolocation is not supported by this browser.");
                }
            }

            // Panggil fungsi untuk menambahkan lokasi pengguna
            addUserLocation();

            // Tambahkan tombol untuk memperbarui lokasi pengguna
            var locationButton = L.control({position: 'topleft'});
            locationButton.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                div.innerHTML = '<a href="#" title="Lokasi Saya" role="button" aria-label="Lokasi Saya" onclick="addUserLocation(); return false;"><span aria-hidden="true">📍</span></a>';
                return div;
            };
            locationButton.addTo(map);

            // Initialize Chart
            var ctx = document.getElementById('equipmentStatusChart').getContext('2d');
            var readyCount = {{ $heavyEquipments->where('status', 'ready')->count() }};
            var operatingCount = {{ $heavyEquipments->where('status', 'beroperasi')->count() }};
            var maintenanceCount = {{ $heavyEquipments->where('status', 'maintenance')->count() }};
            var rusakCount = {{ $heavyEquipments->where('status', 'rusak')->count() }};
            var tidakAdaCount = {{ $heavyEquipments->where('status', 'tidak ada')->count() }};

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Ready', 'Beroperasi', 'Maintenance','Rusak','Tidak Ada'],
                    datasets: [{
                        data: [readyCount, operatingCount, maintenanceCount, rusakCount, tidakAdaCount],
                        backgroundColor: ['#10B981', '#2563EB', '#FFA500','#EF4444','#6B7280'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            let equipmentUsageChart = null;

            function loadEquipmentUsageChart() {
                const selectedYear = document.getElementById('year').value;
            
                fetch(`/api/equipment-usage-history?year=${selectedYear}`)
                    .then(response => response.json())
                    .then(data => {
                        const ctx = document.getElementById('equipmentUsageChart').getContext('2d');
            
                        // Hancurkan chart lama jika ada
                        if (equipmentUsageChart) {
                            equipmentUsageChart.destroy();
                        }
            
                        // Buat chart baru
                        equipmentUsageChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.map(item => item.week),
                                datasets: [{
                                    label: 'Jumlah Pekerjaan',
                                    data: data.map(item => item.count),
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
                                        ticks: {
                                            stepSize: 1
                                        },
                                        grid: {
                                            display: true,
                                            color: '#E5E7EB'
                                        },
                                        title: {
                                            display: true,
                                            text: 'Jumlah Pekerjaan'
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
                                                return `Jumlah: ${context.parsed.y} pekerjaan`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading chart data:', error);
                    });
            }
            
            // Inisialisasi chart saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                loadEquipmentUsageChart();
                
                // Event listener untuk perubahan tahun
                document.getElementById('year').addEventListener('change', function() {
                    loadEquipmentUsageChart();
                });
            });
            fetch('/api/equipment-usage-history')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('equipmentUsageChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.week),
                            datasets: [{
                                label: 'Jumlah Pekerjaan',
                                data: data.map(item => item.count),
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
                                    ticks: {
                                        stepSize: 1
                                    },
                                    grid: {
                                        display: true,
                                        color: '#E5E7EB'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Pekerjaan'
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
                                            return `Jumlah: ${context.parsed.y} pekerjaan`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
        });
        </script>
    @endpush
</x-app-layout>

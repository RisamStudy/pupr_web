<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <!-- Project Selesai -->
                <div class="flex items-center p-6 bg-green-100 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-green-800">Project Selesai</h3>
                        <p class="text-3xl font-bold text-green-900">{{ $endProjects }} Lokasi</p>
                    </div>
                </div>

                <!-- Project Sedang Berlangsung -->
                <div class="flex items-center p-6 bg-yellow-100 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-yellow-800">Project Sedang Berlangsung</h3>
                        <p class="text-3xl font-bold text-yellow-900">{{ $stillProjects }} Lokasi</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="w-full mb-6">
                        <!-- MAPS -->
                        <form action="{{ route('guest.index') }}" method="GET" class="flex items-center mb-2">
                            <label for="year" class="mr-2 font-medium text-gray-700">Filter Tahun:</label>
                            <select id="year" name="year" onchange="this.form.submit()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <div id="map" style="width: 100%; height: 400px;"
                            class="rounded-lg border z-10 border-gray-300"></div>
                    </div>

                    <div class="flex flex-row gap-6 mb-6">
                        <!-- Sedang Bertugas -->
                        <div class="w-1/2">
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg mb-4">
                                <h3 class="font-semibold mb-2 text-yellow"><span
                                        class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Sedang
                                        Bertugas</span></h3>
                                <div class="overflow-y-auto max-h-[255px]">
                                    <table class="table-auto w-full">
                                        <thead>
                                            <tr class="bg-gray-200 sticky top-0 z-10">
                                                <th class="px-4 py-2 text-left">Alat Berat</th>
                                                <th class="px-4 py-2 text-left">Operator</th>
                                                <th class="px-4 py-2 text-left">Helper</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ongoingProjectsMap as $project)
                                                <tr
                                                    class="border-t border-gray-300 hover:bg-gray-50 transition duration-150 ease-in-out">
                                                    <td class="px-4 py-2">
                                                        {{ $project['heavy_equipment']['nomor_lambung'] }}</td>
                                                    <td class="px-4 py-2">
                                                        @foreach ($project['operators'] as $operator)
                                                            <span class="block">{{ $operator }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        @foreach ($project['helpers'] as $helper)
                                                            <span class="block">{{ $helper }}</span>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-8 text-center">
                                                        <div class="flex flex-col items-center justify-center">
                                                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span class="text-gray-500 text-sm">Tidak ada pekerjaan yang
                                                                sedang berlangsung saat ini</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Tersedia -->
                        <div class="flex-1">
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg mb-4 w-full">
                                <h3 class="font-semibold mb-2 text-green"><span
                                        class="bg-green-200 text-green-800 px-2 py-1 rounded-full">Tersedia</span></h3>
                                <div class="overflow-y-auto max-h-[255px]">
                                    <table class="table-auto w-full">
                                        <thead>
                                            <tr class="bg-gray-200 sticky top-0 z-10">
                                                <th class="px-4 py-2 text-left">Nama Personil</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($availableOperators as $operator)
                                                <tr
                                                    class="border-t border-gray-300 hover:bg-gray-50 transition duration-150 ease-in-out">
                                                    <td class="px-4 py-2">
                                                        <span class="block">{{ $operator['name'] }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-6 py-8 text-center">
                                                        <div class="flex flex-col items-center justify-center">
                                                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span class="text-gray-500 text-sm">Tidak ada pekerjaan yang
                                                                sedang berlangsung saat ini</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row gap-6 items-stretch">
                        <!-- Daftar Alat Berat -->
                        <div class="w-2/3 flex flex-col">
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg flex-1 flex flex-col h-full">
                                <h3 class="font-semibold mb-2">Daftar Alat Berat</h3>
                                <div class="bg-gray-100 max-h-[400px] overflow-y-auto">
                                    <table class="table-auto w-full">
                                        <thead>
                                            <tr class="bg-gray-200 sticky top-0 z-10">
                                                <th class="px-4 py-2 text-left">Nomor Lambung</th>
                                                <th class="px-4 py-2 text-left">Nama Alat Berat</th>
                                                <th class="px-4 py-2 text-left">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($heavyEquipments as $equipment)
                                                <tr
                                                    class="border-t border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                                                    <td class="px-4 py-2">{{ $equipment->nomor_lambung }}</td>
                                                    <td class="px-4 py-2">{{ $equipment->name }}</td>
                                                    <td class="px-4 py-2">
                                                        <span
                                                            class="rounded-full text-md font-semibold px-2 py-1
                                                        {{ $equipment->status == 'ready'
                                                            ? 'bg-green-200 text-green-800'
                                                            : ($equipment->status == 'beroperasi'
                                                                ? 'bg-blue-200 text-blue-800'
                                                                : ($equipment->status == 'maintenance'
                                                                    ? 'bg-orange-200 text-orange-800'
                                                                    : ($equipment->status == 'rusak'
                                                                        ? 'bg-red-200 text-red-800'
                                                                        : ''))) }}">
                                                            {{ ucfirst($equipment->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Status Alat Berat -->
                        <div class="w-1/3 flex flex-col">
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg">
                                <h3 class="font-semibold mb-2">Status Alat Berat</h3>
                                <div class="flex flex-col items-center">
                                    <div class="w-full h-64 mb-4">
                                        <canvas id="equipmentStatusChart"></canvas>
                                    </div>
                                    @php
                                        $equipmentTotal = $heavyEquipments->count();
                                        $equipmentPercentage = fn ($count) => $equipmentTotal > 0
                                            ? number_format($count / $equipmentTotal * 100, 1)
                                            : number_format(0, 1);
                                        $readyCount = $heavyEquipments->where('status', 'ready')->count();
                                        $operatingCount = $heavyEquipments->where('status', 'beroperasi')->count();
                                        $maintenanceCount = $heavyEquipments->where('status', 'maintenance')->count();
                                        $damagedCount = $heavyEquipments->where('status', 'rusak')->count();
                                    @endphp
                                    <div class="text-sm">
                                        <p class="font-semibold">Total Alat Berat: {{ $equipmentTotal }}</p>
                                        <p>Ready: {{ $readyCount }} ({{ $equipmentPercentage($readyCount) }}%)</p>
                                        <p>Beroperasi: {{ $operatingCount }} ({{ $equipmentPercentage($operatingCount) }}%)</p>
                                        <p>Maintenance: {{ $maintenanceCount }} ({{ $equipmentPercentage($maintenanceCount) }}%)</p>
                                        <p>Rusak: {{ $damagedCount }} ({{ $equipmentPercentage($damagedCount) }}%)</p>
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
        <style>
            .bg-red-200 {
                background-color: #ffb4b4ff !important;
            }

            .leaflet-control-nav {
                background: white;
                padding: 5px;
                border: 2px solid rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                width: 100px;
                height: 100px;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(3, 1fr);
                gap: 2px;
            }

            .leaflet-control-nav a {
                display: flex;
                justify-content: center;
                align-items: center;
                text-decoration: none;
                color: black;
                font-weight: bold;
                font-size: 18px;
                background: #f4f4f4;
                transition: background 0.3s;
            }

            .leaflet-control-nav a:hover {
                background: #e0e0e0;
            }

            .leaflet-control-nav-up {
                grid-column: 2;
                grid-row: 1;
            }

            .leaflet-control-nav-left {
                grid-column: 1;
                grid-row: 2;
            }

            .leaflet-control-nav-right {
                grid-column: 3;
                grid-row: 2;
            }

            .leaflet-control-nav-down {
                grid-column: 2;
                grid-row: 3;
            }

            .leaflet-control-nav-center {
                grid-column: 2;
                grid-row: 2;
                cursor: default;
            }

            .fancybox__container {
                --fancybox-bg: rgba(24, 24, 27, 0.95);
            }

            .fancybox__content {
                padding: 0;
                background: transparent;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .fancybox__image {
                max-width: 100%;
                max-height: 90vh;
                object-fit: contain;
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

            @media (max-width: 767px) {
                .fancybox__content {
                    padding: 10px;
                }

                .fancybox__image {
                    max-width: 90vw;
                    max-height: 80vh;
                }

                .leaflet-control-nav-container {
                    display: none;
                }
            }

            .fancybox__caption {
                text-align: center;
                max-width: 80%;
                margin: 0 auto;
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
                    margin: 12px 17px !important;
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

            .leaflet-control-attribution {
                display: none !important;
            }
        </style>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
    @endpush
    @push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map
        var map = L.map('map').setView([-6.7, 108.5], 8);

        // Define base layers
        var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        });

        var satelliteLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 30,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        });

        // Set default layer
        osmLayer.addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; PU Cimancis'
        }).addTo(map);

        // Create layer groups instead of marker clusters
        var completedGroup = L.featureGroup();
        var ongoingGroup = L.featureGroup();

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

        // Load markers in batches
        function loadMarkers(projects, group, icon) {
            projects.forEach(function(project) {
                if (project.latitude && project.longitude) {
                    var marker = L.marker([project.latitude, project.longitude], {icon: icon})
                        .bindPopup(createPopupContent(project), {maxWidth: 300, minWidth: 300});

                    marker.on('popupopen', function() {
                        Fancybox.bind(this._popup._contentNode.querySelectorAll("[data-fancybox]"), {
                            dragToClose: false,
                            closeButton: "top-right",
                            Image: {
                                zoom: false,
                            },
                            Toolbar: {
                                display: [
                                    { id: "counter", position: "center" },
                                    "zoom",
                                    "slideshow",
                                    "fullscreen",
                                    "download",
                                    "close",
                                ],
                            },
                            Carousel: {
                                transition: false,
                                friction: 0,
                            },
                            on: {
                                initLayout: (fancybox) => {
                                    fancybox.$container.style.setProperty("--fancybox-height", "100vh");
                                },
                            },
                        });
                    });

                    group.addLayer(marker);
                }
            });
        }

        // Setelah menambahkan marker ke cluster
        Fancybox.bind("[data-fancybox]", {
            dragToClose: false,
            closeButton: "top-right",
            Image: {
                zoom: false,
            },
            Toolbar: {
                display: [
                    { id: "counter", position: "center" },
                    "zoom",
                    "slideshow",
                    "fullscreen",
                    "download",
                    "close",
                ],
            },
            Carousel: {
                transition: false,
                friction: 0,
            },
            on: {
                initLayout: (fancybox) => {
                    fancybox.$container.style.setProperty("--fancybox-height", "100vh");
                },
            },
        });

        // Define icons
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

    // Load markers
    loadMarkers(@json($completedProjectsMap), completedGroup, completedIcon);
    loadMarkers(@json($ongoingProjectsMap), ongoingGroup, ongoingIcon);

        // Add groups to map
        map.addLayer(completedGroup);
        map.addLayer(ongoingGroup);

            // Checkbox filter for project status
            function updateProjectLayers() {
                var completedCheckbox = document.getElementById('completedCheckbox');
                var ongoingCheckbox = document.getElementById('ongoingCheckbox');
                if (completedCheckbox && ongoingCheckbox) {
                    if (completedCheckbox.checked) {
                        if (!map.hasLayer(completedGroup)) map.addLayer(completedGroup);
                    } else {
                        if (map.hasLayer(completedGroup)) map.removeLayer(completedGroup);
                    }
                    if (ongoingCheckbox.checked) {
                        if (!map.hasLayer(ongoingGroup)) map.addLayer(ongoingGroup);
                    } else {
                        if (map.hasLayer(ongoingGroup)) map.removeLayer(ongoingGroup);
                    }
                }
            }

            // Initial call (in case checkboxes are unchecked by default)
            setTimeout(updateProjectLayers, 500);

            // Listen to checkbox changes
            document.addEventListener('change', function(e) {
                if (e.target && (e.target.id === 'completedCheckbox' || e.target.id === 'ongoingCheckbox')) {
                    updateProjectLayers();
                }
            });

        // Fit map to bounds
        var bounds = L.featureGroup([completedGroup, ongoingGroup]).getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds, {padding: [50, 50], maxZoom: 10});
        }

        // Define base maps and overlay maps
        var baseMaps = {
            "Default": osmLayer,
            "Satellite": satelliteLayer
        };

        var overlayMaps = {
            // "Proyek Selesai": completedGroup,
            // "Proyek Sedang Berlangsungs": ongoingGroup
        };
        L.control.layers(baseMaps, overlayMaps).addTo(map);

        // Add legend
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend');
            div.innerHTML = `
                <div class="bg-white p-2 rounded-lg shadow-md">
                    <h4 class="font-semibold mb-2">Status Proyek</h4>
                    <div class="flex items-center mb-1">
                        <input type="checkbox" id="completedCheckbox" class="mr-2">
                        <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png" alt="Completed" style="width: 12px; height: 20px;">
                        <span class="ml-2">Proyek Selesai</span>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="ongoingCheckbox" checked class="mr-2">
                        <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" alt="Ongoing" style="width: 12px; height: 20px;">
                        <span class="ml-2">Proyek Sedang Berlangsung</span>
                    </div>
                </div>
            `;
            return div;
        };
        legend.addTo(map);

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
        var damagedCount = {{ $heavyEquipments->where('status', 'rusak')->count() }};

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Ready', 'Beroperasi', 'Maintenance', 'Rusak'],
                datasets: [{
                    data: [readyCount, operatingCount, maintenanceCount, damagedCount],
                    backgroundColor: ['#10B981', '#2563EB', '#FFA500', '#DC2626']
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
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
    @endpush
</x-guest-layout>

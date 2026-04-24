<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
                        {{ __('Peta Proyek') }}
                    </h2>
                    
                    <div class="bg-white rounded-lg shadow p-4 mb-4">
                        <form action="{{ route('guest.project-map') }}" method="GET" class="flex items-center">
                            <label for="year" class="mr-2 font-medium text-gray-700">Filter Tahun:</label>
                            <select id="year" name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="search" placeholder="Cari proyek atau lokasi..." class="w-full p-2 border rounded-lg">
                    </div>

                    <div id="map" style="width: 100%; height: 500px;" class="rounded-lg border z-10 border-gray-300 mb-4"></div>

                    <div id="search-results" class="bg-gray-100 p-4 rounded-lg mt-4">
                        <!-- Search results will be populated here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
        <style>
            .custom-div-icon {
                background: transparent;
                border: none;
            }
            .marker-pin {
                width: 30px;
                height: 30px;
                border-radius: 50% 50% 50% 0;
                position: absolute;
                transform: rotate(-45deg);
                left: 50%;
                top: 50%;
                margin: -15px 0 0 -15px;
            }
            .marker-pin::after {
                content: '';
                width: 24px;
                height: 24px;
                margin: 3px 0 0 3px;
                background: #fff;
                position: absolute;
                border-radius: 50%;
            }
            .custom-div-icon i {
                position: absolute;
                width: 22px;
                font-size: 14px;
                left: 0;
                right: 0;
                margin: 10px auto;
                text-align: center;
            }
            .marker-pin.completed { background: #4CAF50; }
            .marker-pin.ongoing { background: #FFA500; }
            .custom-div-icon i.fa-check { color: #4CAF50; }
            .custom-div-icon i.fa-clock { color: #FFA500; }
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
                padding: 20px;
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
            .leaflet-control-attribution {
                display: none !important;
            }
        </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([-6.7, 108.5], 9);

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

            var completedIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div class='marker-pin completed'></div><i class='fas fa-check'></i>",
                iconSize: [30, 42],
                iconAnchor: [15, 42]
            });

            var ongoingIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div class='marker-pin ongoing'></div><i class='fas fa-clock'></i>",
                iconSize: [30, 42],
                iconAnchor: [15, 42]
            });

            // Create layer groups instead of marker clusters
            var completedGroup = L.layerGroup();
            var ongoingGroup = L.layerGroup();

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

            @foreach($completedProjects as $project)
                var marker = L.marker([{{ $project->latitude }}, {{ $project->longitude }}], {icon: completedIcon})
                    .bindPopup(createPopupContent({
                        project_name: "{{ $project->project_name }}",
                        village_name: "{{ $project->village->name ?? 'N/A' }}",
                        district_name: "{{ $project->district->name ?? 'N/A' }}",
                        city_name: "{{ $project->city->name ?? 'N/A' }}",
                        tipe_pekerjaan: "{{ $project->tipe_pekerjaan }}",
                        status: "Selesai",
                        image_url: "{{ $project->fieldConditionPhotos->isNotEmpty() ? asset($project->fieldConditionPhotos->first()->photo_path) : '' }}",
                        documentation_link: "{{ $project->documentation_link }}",
                        latitude: "{{ $project->latitude }}",
                        longitude: "{{ $project->longitude }}",
                    }));
                completedGroup.addLayer(marker);
            @endforeach

            @foreach($ongoingProjects as $project)
                var marker = L.marker([{{ $project->latitude }}, {{ $project->longitude }}], {icon: ongoingIcon})
                    .bindPopup(createPopupContent({
                        project_name: "{{ $project->project_name }}",
                        village_name: "{{ $project->village->name ?? 'N/A' }}",
                        district_name: "{{ $project->district->name ?? 'N/A' }}",
                        city_name: "{{ $project->city->name ?? 'N/A' }}",
                        tipe_pekerjaan: "{{ $project->tipe_pekerjaan }}",
                        status: "Sedang Berlangsung",
                        image_url: "{{ $project->fieldConditionPhotos->isNotEmpty() ? asset($project->fieldConditionPhotos->first()->photo_path) : '' }}",
                        documentation_link: "{{ $project->documentation_link }}",
                        latitude: "{{ $project->latitude }}",
                        longitude: "{{ $project->longitude }}",
                    }));
                ongoingGroup.addLayer(marker);
            @endforeach

            map.addLayer(completedGroup);
            map.addLayer(ongoingGroup);

            // Define base maps and overlay maps
            var baseMaps = {
                "Default": osmLayer,
                "Satellite": satelliteLayer
            };

            var overlayMaps = {
                "Proyek Selesai": completedGroup,
                "Proyek Sedang Berlangsung": ongoingGroup
            };

            L.control.layers(baseMaps, overlayMaps).addTo(map);

            var legend = L.control({position: 'bottomright'});
            legend.onAdd = function (map) {
                var div = L.DomUtil.create('div', 'info legend');
                div.innerHTML = `
                    <div class="bg-white p-2 rounded-lg shadow-md">
                        <h4 class="font-semibold mb-2">Status Proyek</h4>
                        <div class="flex items-center mb-1">
                            <span class="w-4 h-4 bg-green-500 rounded-full mr-2"></span>
                            <span>Proyek Selesai</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 bg-secondary rounded-full mr-2"></span>
                            <span>Proyek Sedang Berlangsung</span>
                        </div>
                    </div>
                `;
                return div;
            };
            legend.addTo(map);

            document.getElementById('search').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                if (searchTerm.length > 2) {
                    fetch(`/project-map/search?query=${searchTerm}`)
                        .then(response => response.json())
                        .then(results => {
                            const resultsDiv = document.getElementById('search-results');
                            resultsDiv.innerHTML = '';
                            if (results.length > 0) {
                                results.forEach(result => {
                                    resultsDiv.innerHTML += `
                                        <div class="mb-4 p-4 bg-white rounded shadow flex">
                                            <div class="flex-shrink-0 mr-4">
                                                ${result.image_path ?
                                                    `<a href="${result.image_path}" data-fancybox="search-results" data-caption="${result.project_name}">
                                                        <img src="${result.image_path}" alt="${result.project_name}" class="w-24 h-24 object-cover rounded">
                                                    </a>` :
                                                    `<img src="https://via.placeholder.com/150" alt="No Image Available" class="w-24 h-24 object-cover rounded">`
                                                }
                                            </div>
                                            <div class="flex-grow">
                                                <p class="font-semibold">${result.project_name || 'Nama Proyek Tidak Tersedia'}</p>
                                                <p class="text-sm text-gray-600">${result.alamat || 'Alamat Tidak Tersedia'}</p>
                                                <p class="text-sm text-gray-600">
                                                    ${[result.village_name, result.district_name, result.city_name]
                                                        .filter(Boolean)
                                                        .join(', ') || 'Lokasi Tidak Tersedia'}
                                                </p>
                                                <p class="text-sm">Status: ${result.status || 'Status Tidak Tersedia'}</p>
                                                <p class="text-sm">Tipe: ${result.tipe_pekerjaan || 'Tipe Tidak Tersedia'}</p>
                                                ${result.documentation_link ?
                                                    `<p class="text-sm mt-2"><a href="${result.documentation_link}" target="_blank" class="text-blue-500 hover:underline">Lihat Dokumentasi</a></p>` :
                                                    ''
                                                }
                                            </div>
                                        </div>
                                    `;
                                });
                                var bounds = L.latLngBounds(results.filter(r => r.latitude && r.longitude).map(r => [r.latitude, r.longitude]));
                                if (bounds.isValid()) {
                                    map.fitBounds(bounds);
                                }

                                // Reinitialize Fancybox for search results
                                Fancybox.bind("#search-results [data-fancybox]", {
                                    // Fancybox options here if needed
                                });
                            } else {
                                resultsDiv.innerHTML = '<p>Tidak ada hasil yang ditemukan.</p>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('search-results').innerHTML = '<p>Terjadi kesalahan saat mencari. Silakan coba lagi.</p>';
                        });
                }
            });

            // Inisialisasi Fancybox
            Fancybox.bind("[data-fancybox]", {
                // Opsi Fancybox di sini jika diperlukan
            });

            // Tambahkan event listener untuk popup
            map.on('popupopen', function(e) {
                Fancybox.bind(e.popup._contentNode.querySelectorAll("[data-fancybox]"), {
                    // Opsi Fancybox di sini jika diperlukan
                });
            });
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
        });
    </script>
    @endpush
</x-guest-layout>

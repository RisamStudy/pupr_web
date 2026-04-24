<x-cartrack-layout>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 h-full bg-white border-r p-4 overflow-y-auto">
            <x-text-input class="mb-4" />
            <ul id="vehicle-list" class="space-y-3">
            </ul>
        </aside>

        <aside class="hidden w-64 h-full bg-white border-r p-4 overflow-y-auto" id="detailAside">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="font-semibold text-lg">Detail Kendaraan</h2>
                <button id="closeAside" class="text-slate-500 hover:text-slate-700">
                    ✕
                </button>
            </div>
            <div class="flex border-b">
                <span id="registration"></span>
            </div>

        </aside>
        <div id="map" class="w-full h-full"></div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        <style>
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let detailAside = document.getElementById('detailAside');

                // Menampilkan sidebar detail
                function openAside(data) {
                    detailAside.classList.remove("hidden");
                    console.log(data);
                    document.getElementById('registration').textContent = data.registration;
                }

                // Menyembunyikan sidebar detail
                document.getElementById('closeAside').addEventListener('click', function() {
                    detailAside.classList.add("hidden");
                })

                // Inisisasi Peta Leaflet
                const map = L.map('map').setView([-6.200000, 106.816666], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map);

                // ikon mobil
                const carIcon = L.icon({
                    iconUrl: "https://cdn-icons-png.flaticon.com/512/61/61168.png",
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                    popupAnchor: [0, -16]
                });

                const markers = {};
                const polylines = {};

                function getColorForVehicle(vehicleId) {
                    // daftar warna tetap biar konsisten (maks 10 kendaraan, sisanya random)
                    const colors = [
                        "blue", "red", "green", "orange", "purple",
                        "brown", "pink", "black", "teal", "cyan"
                    ];
                    const index = vehicleId % colors.length;
                    return colors[index];
                }

                async function loadData() {
                    const res = await fetch('/tracking/data');
                    const vehicles = await res.json();

                    const listEl = document.getElementById("vehicle-list");
                    listEl.innerHTML = "";

                    vehicles.forEach(v => {
                        if (!v.positions || v.positions.length === 0) return;

                        // ambil trip terakhir
                        const last = v.positions[v.positions.length - 1];
                        const lastLat = parseFloat(last.end_latitude ?? last.start_latitude);
                        const lastLon = parseFloat(last.end_longitude ?? last.start_longitude);

                        // marker
                        if (!markers[v.vehicle_id]) {
                            markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                                    icon: carIcon
                                })
                                .addTo(map)
                                .bindPopup(
                                    `<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`);
                        } else {
                            markers[v.vehicle_id].setLatLng([lastLat, lastLon])
                                .setPopupContent(
                                    `<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`);
                        }

                        // sidebar list
                        const li = document.createElement("li");
                        li.className = "p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer";
                        li.innerHTML = `<div class="font-semibold">${v.registration}</div>
                    <div class="text-xs text-gray-600">Lat: ${lastLat.toFixed(5)} | Lon: ${lastLon.toFixed(5)}</div>`;
                        li.onclick = function() {
                            openAside(v);
                            for (const vehicleId in polylines) {
                                // Pengecekan keamanan: memastikan properti itu milik objek, bukan dari prototype
                                if (polylines.hasOwnProperty(vehicleId)) {
                                    // 2. Dapatkan objek polyline
                                    const polyline = polylines[vehicleId];

                                    // 3. Hapus polyline dari peta
                                    if (polyline) {
                                        polyline.remove();
                                    }
                                }
                            }

                            // polyline (riwayat perjalanan berdasarkan start & end setiap trip)
                            const coords = [];
                            v.positions.forEach(p => {
                                if (p.start_latitude && p.start_longitude) {
                                    coords.push([parseFloat(p.start_latitude), parseFloat(p
                                        .start_longitude)]);
                                }
                                if (p.end_latitude && p.end_longitude) {
                                    coords.push([parseFloat(p.end_latitude), parseFloat(p
                                        .end_longitude)]);
                                }
                            });

                            if (coords.length > 0) {
                                const color = getColorForVehicle(v.vehicle_id);
                                if (!polylines[v.vehicle_id]) {
                                    polylines[v.vehicle_id] = L.polyline(coords, {
                                        color: color,
                                        weight: 3
                                    }).addTo(map);
                                } else {
                                    polylines[v.vehicle_id].setLatLngs(coords);
                                    polylines[v.vehicle_id].setStyle({
                                        color: color
                                    });
                                }
                            }
                            map.setView([lastLat, lastLon], 15, {
                                animate: true
                            });
                        }
                        listEl.appendChild(li);
                    });
                }

                loadData();
            });
        </script>
    @endpush
</x-cartrack-layout>

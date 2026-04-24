<x-cartrack-layout>
    <div class="flex h-screen" x-data="trackingMap()" x-init="initMap()">
        <!-- Sidebar -->
        <aside class="w-96 h-full bg-white border-r p-4 overflow-y-auto">
            <x-text-input class="mb-4" id="vehilce-search" x-model="searchQuery" placeholder="Cari kendaraan..." />
            <div class="flex">
                <div class="text-sm text-gray-600" x-text="vehicles.length + ' kendaraan'"></div>
            </div>
            <ul class="space-y-3 border-t mt-4">
                <template x-for="vehicle in filteredVehicles" :key="vehicle.vehicle_id">
                    <li class="p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer"
                        @click="showDetail(vehicle)" @hover="alert('hover')">
                        <div class="font-semibold" x-text="vehicle.registration"></div>
                        <div class="text-xs text-gray-600" x-text="formatLatLon(vehicle)"></div>
                    </li>
                </template>
            </ul>
        </aside>

        <!-- Detail Sidebar -->
        <aside class="hidden w-96 h-full bg-white border-r p-2 overflow-y-auto" :class="{ 'hidden': !detailVehicle }">
            <div class="p-2 border-b flex justify-between items-center">
                <h2 class="font-semibold text-md">Detail Kendaraan</h2>
                <button class="text-slate-500 hover:text-slate-700" @click="detailVehicle = null">✕</button>
            </div>
            <div class="flex flex-col border-b p-2">
                <span x-text="detailVehicle?.registration"></span>
                <span class="text-xs"
                    x-text="detailVehicle?.manufacturer + ' ' + detailVehicle?.model + ' ' + detailVehicle?.model_year + ' ' + detailVehicle?.colour"></span>
            </div>
            <div class="">
                <h3 class="font-semibold text-md p-2 border-b">Aktivitas</h3>
                <x-text-input id="dateRangeInput" placeholder="Periode" class="mt-2" />
                <!-- Nav Tabs -->
                <div class="flex justify-between mt-2 mb-2 border-b pb-2">
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="tab === 'semua' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="tab = 'semua'">
                        Semua
                    </button>
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="tab === 'berjalan' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="tab = 'berjalan'">
                        Berjalan
                    </button>
                </div>
                <!-- End Nav Tabs -->
                <!-- Tab Content -->
                <div class="mt-2">
                    <template x-if="tab === 'semua'">
                        <ul class="space-y-2">
                            <template x-for="pos in detailVehicle?.positions" :key="pos.id">
                                <li class="border rounded p-2 bg-gray-50">
                                    <div class="text-xs text-gray-600"
                                        x-text="'Dari: ' + (pos.start_latitude ? pos.start_latitude + ', ' + pos.start_longitude : '-')">
                                    </div>
                                    <div class="text-xs text-gray-600"
                                        x-text="'Ke: ' + (pos.end_latitude ? pos.end_latitude + ', ' + pos.end_longitude : '-')">
                                    </div>
                                    <div class="text-xs text-gray-600"
                                        x-text="'Waktu Mulai: ' + (pos.start_time ? new Date(pos.start_time).toLocaleString() : '-')">
                                    </div>
                                    <div class="text-xs text-gray-600"
                                        x-text="'Waktu Selesai: ' + (pos.end_time ? new Date(pos.end_time).toLocaleString() : '-')">
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="tab === 'berjalan'">
                        <div>
                            <div class="mb-4 flex gap-2 flex-wrap">
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Trip</div>
                                    <div class="text-lg"
                                        x-text="detailVehicle?.positions?.filter(p => !p.end_time).length || 0"></div>
                                </div>
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Jarak</div>
                                    <div class="text-lg"
                                        x-text="totalDistance(detailVehicle?.positions?.filter(p => !p.end_time))">
                                    </div>
                                    <span class="text-xs font-normal">km</span>
                                </div>
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Waktu</div>
                                    <div class="text-lg"
                                        x-text="totalDuration(detailVehicle?.positions?.filter(p => !p.end_time))">
                                    </div>
                                </div>
                            </div>
                            <ul class="space-y-2">
                                <template x-for="pos in detailVehicle?.positions?.filter(p => !p.end_time)"
                                    :key="pos.id">
                                    <li class="border rounded p-2 bg-yellow-50">
                                        <div class="font-semibold text-yellow-700">Sedang Berjalan</div>
                                        <div class="text-xs text-gray-600"
                                            x-text="'Dari: ' + (pos.start_latitude ? pos.start_latitude + ', ' + pos.start_longitude : '-')">
                                        </div>
                                        <div class="text-xs text-gray-600"
                                            x-text="'Waktu Mulai: ' + (pos.start_time ? new Date(pos.start_time).toLocaleString() : '-')">
                                        </div>
                                    </li>
                                </template>
                                <template x-if="!(detailVehicle?.positions?.some(p => !p.end_time))">
                                    <li class="text-xs text-gray-400 p-2">Tidak ada perjalanan berjalan.</li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
                <!-- End Tab Content -->
                {{-- <ul class="space-y-2 border-t mt-2">
                    <template x-for="pos in detailVehicle?.positions" :key="pos.id">
                        <li class="border rounded p-2 bg-gray-50">
                            <div class="text-xs text-gray-600"
                                x-text="'Dari: ' + (pos.start_latitude ? pos.start_latitude + ', ' + pos.start_longitude : '-')">
                            </div>
                            <div class="text-xs text-gray-600"
                                x-text="'Ke: ' + (pos.end_latitude ? pos.end_latitude + ', ' + pos.end_longitude : '-')">
                            </div>
                            <div class="text-xs text-gray-600"
                                x-text="'Waktu Mulai: ' + (pos.start_time ? new Date(pos.start_time).toLocaleString() : '-')">
                            </div>
                            <div class="text-xs text-gray-600"
                                x-text="'Waktu Selesai: ' + (pos.end_time ? new Date(pos.end_time).toLocaleString() : '-')">
                            </div>
                        </li>
                    </template>
                </ul> --}}
            </div>
        </aside>

        <!-- Map -->
        <div id="map" class="w-full h-full"></div>

        <!-- Detail Tambahan di Bawah Map -->
        <div x-show="detailVehicle" x-transition
            class="fixed right-0 bottom-0 z-[1000] bg-white border-t shadow-lg p-6  mx-auto rounded-t-lg"
            style="display: none;">
            <h3 class="font-bold text-lg mb-2">Detail Tambahan Kendaraan</h3>
            {{-- Silakan isi detail tambahan di sini --}}
            <div>
                <span class="font-semibold">Nomor Polisi:</span>
                <span x-text="detailVehicle?.registration"></span>
            </div>
            <div>
                <span class="font-semibold">Merk & Model:</span>
                <span x-text="detailVehicle?.manufacturer + ' ' + detailVehicle?.model"></span>
            </div>
            <!-- Tambahkan detail lain sesuai kebutuhan -->
        </div>
        <!-- End Detail Tambahan -->
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            function trackingMap() {
                return {
                    map: null,
                    vehicles: [],
                    detailVehicle: null,
                    markers: {},
                    polylines: {},
                    searchQuery: '',
                    tab: 'semua',

                    get filteredVehicles() {
                        if (!this.searchQuery) return this.vehicles;
                        const q = this.searchQuery.toLowerCase();
                        return this.vehicles.filter(v =>
                            (v.registration && v.registration.toLowerCase().includes(q)) ||
                            (v.vehicle_id && String(v.vehicle_id).includes(q))
                        );
                    },

                    totalDistance(positions) {
                        // Asumsi setiap posisi punya properti distance_km
                        if (!positions) return 0;
                        return positions.reduce((sum, p) => sum + (parseFloat(p.distance_km) || 0), 0).toFixed(2);
                    },

                    totalDuration(positions) {
                        // Hitung total waktu dari start_time sampai sekarang (karena belum selesai)
                        if (!positions) return '0 menit';
                        let totalMs = 0;
                        positions.forEach(p => {
                            if (p.start_time) {
                                const start = new Date(p.start_time);
                                const end = new Date(); // sekarang
                                totalMs += end - start;
                            }
                        });
                        // Konversi ms ke jam dan menit
                        const totalMinutes = Math.floor(totalMs / 60000);
                        const hours = Math.floor(totalMinutes / 60);
                        const minutes = totalMinutes % 60;
                        if (hours > 0) {
                            return `${hours} jam ${minutes} menit`;
                        }
                        return `${minutes} menit`;
                    },

                    initMap() {
                        this.map = L.map('map').setView([-6.200000, 106.816666], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                        }).addTo(this.map);

                        this.loadData();

                        // inisialisasi flatpickr
                        flatpickr("#dateRangeInput", {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            defaultDate: [
                                new Date().setDate(new Date().getDate() - 7),
                                new Date()
                            ],
                            onChange: function(selectedDates, dateStr, instance) {
                                // Kalau sudah pilih 2 tanggal (start & end)
                                if (selectedDates.length === 2) {
                                    const startDate = selectedDates[0].toISOString().split("T")[0];
                                    const endDate = selectedDates[1].toISOString().split("T")[0];

                                    alert(`Kamu memilih range:\nStart: ${startDate}\nEnd: ${endDate}`);
                                }
                            }
                        });
                    },

                    async loadData() {
                        const res = await fetch('/tracking/data');
                        this.vehicles = await res.json();

                        this.vehicles.forEach(v => {
                            if (!v.positions?.length) return;

                            const last = v.positions[v.positions.length - 1];
                            const lastLat = parseFloat(last.end_latitude ?? last.start_latitude);
                            const lastLon = parseFloat(last.end_longitude ?? last.start_longitude);

                            // marker
                            if (!this.markers[v.vehicle_id]) {
                                this.markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                                        icon: this.getCarIcon()
                                    }).addTo(this.map)
                                    .bindPopup(`<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`)
                                    .on('click', () => {
                                        this.showDetail(v);
                                    });
                            } else {
                                this.markers[v.vehicle_id].setLatLng([lastLat, lastLon])
                                    .setPopupContent(
                                        `<b>${v.registration}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`)
                                    .on('click', () => {
                                        this.showDetail(v);
                                    });
                            }
                        });
                    },

                    showDetail(vehicle) {
                        this.detailVehicle = vehicle;

                        this.tab = 'semua';

                        // Hari ini
                        const endDate = new Date();

                        // Seminggu kebelakang
                        const startDate = new Date();
                        startDate.setDate(endDate.getDate() - 7);

                        // Format ke YYYY-MM-DD (misalnya untuk query param API)
                        const formatDate = (date) => {
                            return date.toISOString().split("T")[0];
                        };

                        const params = {
                            startDate: formatDate(startDate),
                            endDate: formatDate(endDate),
                        };

                        console.log(params);
                        // { startDate: "2025-09-21", endDate: "2025-09-28" }


                        // hapus semua polyline lama
                        for (const vehicleId in this.polylines) {
                            if (this.polylines[vehicleId]) this.polylines[vehicleId].remove();
                        }

                        // buat polyline perjalanan kendaraan yang dipilih
                        const coords = [];
                        vehicle.positions.forEach(p => {
                            if (p.start_latitude && p.start_longitude) {
                                coords.push([parseFloat(p.start_latitude), parseFloat(p.start_longitude)]);
                            }
                            if (p.end_latitude && p.end_longitude) {
                                coords.push([parseFloat(p.end_latitude), parseFloat(p.end_longitude)]);
                            }
                        });

                        if (coords.length > 0) {
                            const color = this.getColorForVehicle(vehicle.vehicle_id);
                            this.polylines[vehicle.vehicle_id] = L.polyline(coords, {
                                color: color,
                                weight: 3
                            }).addTo(this.map);

                            const last = coords[coords.length - 1];
                            this.map.setView(last, 15, {
                                animate: true
                            });
                        }
                    },

                    formatLatLon(v) {
                        if (!v.positions?.length) return "";
                        const last = v.positions[v.positions.length - 1];
                        const lat = parseFloat(last.end_latitude ?? last.start_latitude);
                        const lon = parseFloat(last.end_longitude ?? last.start_longitude);
                        return `Lat: ${lat.toFixed(5)} | Lon: ${lon.toFixed(5)}`;
                    },

                    getCarIcon() {
                        return L.icon({
                            iconUrl: "https://cdn-icons-png.flaticon.com/512/61/61168.png",
                            iconSize: [32, 32],
                            iconAnchor: [16, 16],
                            popupAnchor: [0, -16]
                        });
                    },

                    getColorForVehicle(vehicleId) {
                        const colors = [
                            "blue", "red", "green", "orange", "purple",
                            "brown", "pink", "black", "teal", "cyan"
                        ];
                        return colors[vehicleId % colors.length];
                    }
                }
            }
        </script>
    @endpush
</x-cartrack-layout>

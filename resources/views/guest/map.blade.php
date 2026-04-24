<x-cartrack-layout>
    <div class="flex h-screen" x-data="trackingMap()" x-init="initPageCartrack()">
        <!-- Sidebar -->

        <aside class="w-96 h-full bg-white border-r overflow-y-auto">
            <!-- Sticky Header -->
            <div class="sticky top-0 bg-white z-10 p-4 border-b">
                <!-- Nav Tabs -->
                <div class="flex justify-between mb-4">
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="asideTab === 'proyek' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="asideTab = 'proyek'; loadDataProjects()">
                        Proyek
                    </button>
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="asideTab === 'kendaraan' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="asideTab = 'kendaraan'; loadData()">
                        Kendaraan
                    </button>
                </div>
                <!-- End Nav Tabs -->

                <!-- Tab Proyek Header -->
                <template x-if="asideTab === 'proyek'">
                    <div>
                        <h2 class="font-semibold text-lg mb-4">Daftar Proyek</h2>
                        <select id="year" name="year" x-model="selectedYear"
                            class="rounded-md mb-4 border-gray-300 w-full shadow-sm sm:text-sm focus:border-primary-500 focus:ring focus:ring-primary-500">
                            <template x-for="year in years" :key="year">
                                <option :value="year" x-text="year"></option>
                            </template>
                        </select>
                        <x-text-input class="mb-4" id="project-search" x-model="searchQueryProject"
                            placeholder="Cari proyek atau lokasi..." />
                    </div>
                </template>

                <!-- Tab Kendaraan Header -->
                <template x-if="asideTab === 'kendaraan'">
                    <div>
                        <h2 class="font-semibold text-lg mb-4">Daftar Kendaraan</h2>
                        <x-text-input class="mb-4" id="vehilce-search" x-model="searchQuery"
                            placeholder="Cari kendaraan..." />
                        <div class="flex">
                            <div class="text-sm text-gray-600" x-text="vehicles.length + ' kendaraan'"></div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- End Sticky Header -->

            <!-- Scrollable Content -->
            <div class="p-4">
                <template x-if="asideTab === 'proyek'">
                    <div id="project-list">
                        <template x-if="projects.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm">Tidak ada proyek ditemukan</p>
                            </div>
                        </template>
                        <template x-for="project in projects" :key="project.id">
                            <div class="mb-4 p-4 bg-white rounded shadow flex hover:shadow-lg transition cursor-pointer"
                                @click="
                            map.setView([parseFloat(project.latitude), parseFloat(project.longitude)], 15, { animate: true });
                            if (projectMarkers[project.id]) {
                                projectMarkers[project.id].openPopup();
                            }
                        ">
                                <div class="flex-shrink-0 mr-4">
                                    <template x-if="project.image_url">
                                        <img :src="project.image_url" :alt="project.project_name"
                                            class="w-24 h-24 object-cover rounded">
                                    </template>
                                    <template x-if="!project.image_url">
                                        <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-grow">
                                    <p class="font-semibold"
                                        x-text="project.project_name || 'Nama Proyek Tidak Tersedia'"></p>
                                    <p class="text-sm text-gray-600"
                                        x-text="project.address || 'Alamat Tidak Tersedia'"></p>
                                    <p class="text-sm text-gray-600">
                                        <span x-text="project.village_name"></span>,
                                        <span x-text="project.district_name"></span>,
                                        <span x-text="project.city_name"></span>
                                    </p>
                                    <p class="text-sm">
                                        <span>Status: </span>
                                        <span class="font-semibold"
                                            :class="project.status === 'Selesai' ? 'text-green-600' : 'text-orange-600'"
                                            x-text="project.status || 'Status Tidak Tersedia'"></span>
                                    </p>
                                    <p class="text-sm"
                                        x-text="'Tipe: ' + (project.project_type || 'Tipe Tidak Tersedia')"></p>
                                    <template x-if="project.documentation_link">
                                        <p class="text-sm mt-2">
                                            <a :href="project.documentation_link" target="_blank"
                                                class="text-blue-500 hover:underline" @click.stop>Lihat Dokumentasi</a>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="asideTab === 'kendaraan'">
                    <ul class="space-y-3">
                        <template x-for="vehicle in filteredVehicles" :key="vehicle.vehicle_id">
                            <li class="p-3 rounded border bg-gray-50 hover:bg-gray-100 cursor-pointer"
                                @click="showDetail(vehicle)">
                                <div class="font-semibold"
                                    x-text="vehicle.heavy_equipment.length > 0 ? vehicle.heavy_equipment[0].name : vehicle.manufacturer + ' ' +vehicle.model + ' ' + vehicle.model_year + ' ' + vehicle.colour">
                                </div>
                                <div class="text-xs text-gray-600" x-text="formatLatLon(vehicle)"></div>
                            </li>
                        </template>
                    </ul>
                </template>
            </div>
            <!-- End Scrollable Content -->
        </aside>

        <!-- Detail Sidebar -->
        <aside class="hidden w-96 h-full bg-white border-r p-2 overflow-y-auto" :class="{ 'hidden': !detailVehicle }">
            <div class="p-2 border-b flex justify-between items-center">
                <h2 class="font-semibold text-md">Detail Kendaraan</h2>
                <button class="text-slate-500 hover:text-slate-700"
                    @click="detailVehicle = null; currentVehicle = null">✕</button>
            </div>
            <div class="flex flex-col border-b p-2">
                <span x-text="currentVehicle?.registration"></span>
                <span class="text-xs"
                    x-text="currentVehicle?.manufacturer + ' ' + currentVehicle?.model + ' ' + currentVehicle?.model_year + ' ' + currentVehicle?.colour"></span>
            </div>
            <div class="">
                <h3 class="font-semibold text-md p-2 border-b">Aktivitas</h3>

                <!-- Date Range Picker -->
                <x-text-input id="dateRangeInput" placeholder="Periode" class="mt-2" />

                <!-- Nav Tabs -->
                <div class="flex justify-between mt-2 mb-2 border-b pb-2">
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="tab === 'perhari' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="tab = 'perhari'">
                        Per Hari
                    </button>
                    <button class="px-4 py-2 rounded font-semibold transition-colors"
                        :class="tab === 'semua' ? 'bg-blue-600 text-white shadow' :
                            'bg-gray-200 text-gray-700 hover:bg-blue-100'"
                        @click="tab = 'semua'">
                        Semua
                    </button>
                </div>
                <!-- End Nav Tabs -->

                <!-- Tab Content -->
                <div class="mt-2">
                    <template x-if="tab === 'perhari'">
                        <ul class="space-y-2">
                            <!-- show newest on top but keep dayList chronological for charts -->
                            <template x-for="day in dayList.slice().reverse()" :key="day.date">
                                <li class="border rounded p-2 bg-gray-50">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-semibold text-black text-md" x-text="day.label"></div>
                                        <button @click="showDetailForDay(day.date)"
                                            class="text-xs text-blue-600">Detail hari</button>
                                    </div>
                                    <div style="height: 120px;">
                                        <canvas :id="`perhariDurationChart-${day.date}`"></canvas>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="tab === 'semua'">
                        <div>
                            <div class="mb-4 flex gap-2 flex-wrap">
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Trip</div>
                                    <div class="text-lg" x-text="getOngoingTrips().length"></div>
                                </div>
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Jarak</div>
                                    <div class="text-lg" x-text="getTotalDistance(getOngoingTrips())"></div>
                                    <span class="text-xs font-normal">km</span>
                                </div>
                                <div
                                    class="flex-1 px-4 py-2 rounded bg-gray-600 text-white font-semibold text-center shadow">
                                    <div class="text-xs font-normal">Total Waktu</div>
                                    <div class="text-lg" x-text="getTotalDuration(getOngoingTrips())"></div>
                                </div>
                            </div>
                            <ul class="space-y-2">
                                <template x-for="day in dayList">
                                    <li class="border rounded p-2 bg-gray-50">
                                        <div class="font-semibold text-black text-md" x-text="day.label">
                                        </div>
                                        <p>Total trip distance: <span x-text="day.totalDistance"></span> km</p>
                                        <p>Total idle time: <span x-text="formatSeconds(day.totalIdleSeconds)"></span>
                                        </p>
                                        <p>Total trips: <span x-text="day.totalTrips"></span></p>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
                <!-- End Tab Content -->

            </div>
        </aside>

        <!-- Map -->
        <div id="map" class="w-full h-full">
            <!-- Loading Overlay -->
            <div x-show="isLoading" x-transition
                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-[9999]"
                style="display: none;">
                <div class="text-center">
                    <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-600 font-semibold">Memuat data...</p>
                </div>
            </div>
        </div>

        <!-- Detail Tambahan di Bawah Map -->
        <div x-show="detailVehicle" x-transition class="fixed bottom-0 z-[1000] bg-white border-t shadow-lg"
            :style="`left: ${detailVehicle ? '768px' : '384px'}; right: 0; height: ${chartHeight}px;`"
            style="display: none; min-height: 200px; max-height: 600px;">

            <!-- Resize Handle -->
            <div @mousedown="startResize($event)"
                class="absolute top-0 left-0 right-0 h-2 cursor-ns-resize hover:bg-blue-200 flex items-center justify-center group">
                <div class="w-12 h-1 bg-gray-300 rounded group-hover:bg-blue-500 transition"></div>
            </div>

            <div class="p-4 h-full flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-base">
                        Monitoring Kendaraan -
                        <span class="text-sm"
                            x-text="tab === 'semua'
                                        ? (new Date(startDate).toLocaleDateString('id-ID') + ' - ' + new Date(endDate).toLocaleDateString('id-ID'))
                                        : (
                                            (_singleDayTrips && _singleDayTrips.length > 0)
                                            ? (() => {
                                                // Cari day di dayList yang tanggalnya sama dengan trip
                                                const tripDate = _singleDayTrips[0]?.start_timestamp?.split('T')[0];
                                                const dayObj = dayList.find(d => d.date === tripDate);
                                                return dayObj ? dayObj.label + ' (' + new Date(dayObj.date).toLocaleDateString('id-ID') + ')' : (new Date(startDate).toLocaleDateString('id-ID') + ' - ' + new Date(endDate).toLocaleDateString('id-ID'));
                                            })()
                                            : (new Date(startDate).toLocaleDateString('id-ID') + ' - ' + new Date(endDate).toLocaleDateString('id-ID'))
                                        )
                                    ">
                        </span>
                    </h3>
                    <button @click="detailVehicle = null; currentVehicle = null; destroyVehicleChart();"
                        class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg relative flex-1 overflow-y-auto">
                    <!-- Loading Overlay untuk Chart -->
                    <div x-show="isChartLoading" x-transition
                        class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center rounded-lg z-10">
                        <div class="text-center">
                            <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-xs text-gray-600 font-semibold">Memuat data monitoring...</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-2">
                        <div class="flex gap-4 text-xs">

                        </div>
                        <div class="flex gap-4 text-xs">
                            <div>
                                <p x-text="summaryStats.berhenti"></p>
                                <p>Berhenti</p>
                            </div>
                            <div>
                                <p x-text="summaryStats.kilometer"></p>
                                <p>Kilometer</p>
                            </div>
                            <div>
                                <p x-text="summaryStats.mengemudi"></p>
                                <p>Mengemudi</p>
                            </div>
                            <div>
                                <p x-text="summaryStats.idle"></p>
                                <p>Idle</p>
                            </div>
                            <div>
                                <p x-text="summaryStats.starter"></p>
                                <p>Starter</p>
                            </div>
                        </div>
                    </div>

                    <div :style="`height: ${chartCanvasHeight}px;`" class="min-h-[120px]">
                        <canvas id="batteryEventChart"></canvas>
                    </div>
                    <div :style="`height: ${chartCanvasHeight}px;`" class="min-h-[120px]">
                        <canvas id="fuelEventChart"></canvas>
                    </div>
                    <div :style="`height: ${chartCanvasHeight}px;`" class="min-h-[120px]">
                        <canvas id="ptoDetailChart"></canvas>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Detail Tambahan -->
    </div>

    @push('styles')
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

            .marker-pin.completed {
                background: #4CAF50;
            }

            .marker-pin.ongoing {
                background: #FFA500;
            }

            .custom-div-icon i.fa-check {
                color: #4CAF50;
            }

            .custom-div-icon i.fa-clock {
                color: #FFA500;
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

            /* Resize Handle Styles */
            .cursor-ns-resize {
                cursor: ns-resize !important;
            }

            .cursor-ns-resize:active {
                cursor: ns-resize !important;
            }

            /* Prevent text selection during resize */
            .resizing * {
                user-select: none !important;
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function trackingMap() {
                return {
                    map: null,
                    vehicles: [],
                    detailVehicle: null,
                    currentVehicle: null,
                    markers: {},
                    projectMarkers: {},
                    polylines: {},
                    searchQuery: '',
                    asideTab: 'proyek',
                    tab: 'perhari',
                    startDate: (() => {
                        const d = new Date();
                        d.setDate(d.getDate() - 7);
                        return d;
                    })(),
                    endDate: new Date(),
                    years: [],
                    selectedYear: new Date().getFullYear(),
                    projects: [],
                    searchQueryProject: '',
                    isLoading: false, // Tambahkan ini
                    isChartLoading: false,

                    ptoRawData: [],
                    fuelRawData: [],
                    batteryRawData: [],

                    dayList: [],
                    perhariListCharts: {},
                    ptoDetailChart: null,
                    fuelEventChart: null,
                    batteryEventChart: null,

                    _singleDayTrips: [],

                    // Tambahkan properti untuk resize
                    chartHeight: 300, // Default height 300px
                    isResizing: false,
                    startY: 0,
                    startHeight: 0,

                    // Computed property untuk canvas height
                    get chartCanvasHeight() {
                        // Canvas height = total height - padding - header - stats
                        return this.chartHeight - 140; // 140px untuk padding, header, stats
                    },

                    // Method untuk start resize
                    startResize(event) {
                        this.isResizing = true;
                        this.startY = event.clientY;
                        this.startHeight = this.chartHeight;

                        event.preventDefault();

                        const handleMove = (e) => {
                            if (!this.isResizing) return;

                            const deltaY = this.startY - e.clientY;
                            let newHeight = this.startHeight + deltaY;
                            newHeight = Math.max(200, Math.min(600, newHeight));

                            this.chartHeight = newHeight;

                            if (this.vehicleMonitoringChart) {
                                this.vehicleMonitoringChart.resize();
                            }
                        };

                        const handleUp = () => {
                            this.isResizing = false;
                            document.removeEventListener('mousemove', handleMove);
                            document.removeEventListener('mouseup', handleUp);
                        };

                        document.addEventListener('mousemove', handleMove);
                        document.addEventListener('mouseup', handleUp);
                    },

                    init() {
                        // Watch untuk searchQueryProject
                        this.$watch('searchQueryProject', (value) => {
                            if (value.length >= 3 || value.length === 0) {
                                this.searchProjects();
                            }
                        });

                        // Watch untuk selectedYear
                        this.$watch('selectedYear', (value) => {
                            if (this.asideTab === 'proyek') {
                                this.searchProjects();
                            }
                        });

                        // Watch untuk tab changes
                        this.$watch('tab', (val) => {
                            this.isChartLoading = true;
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.destroyVehicleChart();
                                    const ptoCanvas = document.getElementById('ptoDetailChart');
                                    if (ptoCanvas) this.initPTODetailChart();
                                    const batteryCanvas = document.getElementById('batteryEventChart');
                                    if (batteryCanvas) this.initBatteryEventChart();
                                    const fuelCanvas = document.getElementById('fuelEventChart');
                                    if (fuelCanvas) this.initFuelEventChart();
                                    this.initPerhariListCharts();
                                    this.isChartLoading = false;
                                }, 50);
                            });
                        });
                    },

                    destroyVehicleChart() {
                        if (this.batteryEventChart) {
                            this.batteryEventChart.destroy();
                            this.batteryEventChart = null;
                        }

                        if (this.fuelEventChart) {
                            this.fuelEventChart.destroy();
                            this.fuelEventChart = null;
                        }

                        if (this.ptoDetailChart) {
                            this.ptoDetailChart.destroy();
                            this.ptoDetailChart = null;
                        }

                        // destroy perhari list charts
                        this.destroyPerhariListCharts();

                        // clear single-day trips
                        this._singleDayTrips = [];
                    },

                    async searchProjects() {
                        this.isLoading = true;

                        // Hapus semua marker proyek
                        this.map.eachLayer((layer) => {
                            if (layer instanceof L.Marker) {
                                this.map.removeLayer(layer);
                            }
                        });

                        // Reset projectMarkers
                        this.projectMarkers = {};

                        try {
                            const params = new URLSearchParams({
                                year: this.selectedYear,
                                query: this.searchQueryProject
                            });

                            const res = await fetch(`/api/projects?${params.toString()}`);
                            this.projects = await res.json();

                            console.log('Search results:', this.projects);

                            // Tambahkan marker untuk hasil pencarian
                            this.projects.forEach(proj => {
                                if (!proj.latitude || !proj.longitude) return;

                                const lat = parseFloat(proj.latitude);
                                const lon = parseFloat(proj.longitude);

                                // Simpan marker ke projectMarkers
                                this.projectMarkers[proj.id] = L.marker([lat, lon], {
                                        icon: this.getProjectIcon(proj.status)
                                    }).addTo(this.map)
                                    .bindPopup(this.getPopupContenForProject(proj));
                            });

                            // Jika ada hasil, zoom ke proyek pertama
                            if (this.projects.length > 0 && this.projects[0].latitude && this.projects[0].longitude) {
                                this.map.setView([
                                    parseFloat(this.projects[0].latitude),
                                    parseFloat(this.projects[0].longitude)
                                ], 13);
                            }
                        } catch (error) {
                            console.error('Error searching projects:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    get filteredVehicles() {
                        if (!this.searchQuery) return this.vehicles;
                        const q = this.searchQuery.toLowerCase();

                        return this.vehicles.filter(v => {
                            // gabungkan semua field yang relevan untuk pencarian
                            const searchable = [
                                v.registration,
                                v.vehicle_id,
                                v.manufacturer,
                                v.model,
                                v.model_year,
                                v.colour,
                                v.heavy_equipment?.length > 0 ? v.heavy_equipment[0].name : null
                            ].filter(Boolean).join(" ").toLowerCase();

                            return searchable.includes(q);
                        });
                    },

                    get summaryStats() {
                        let trips = [];

                        if (this.tab === 'perhari' && Array.isArray(this._singleDayTrips) && this._singleDayTrips.length >
                            0) {
                            // Jika perhari dan sudah pilih hari, ambil trip hari itu saja
                            trips = this._singleDayTrips;
                        } else if (this.tab === 'perhari') {
                            // Jika perhari tapi belum pilih hari, ambil semua trip dalam range
                            trips = Array.isArray(this.detailVehicle) ? this.detailVehicle : [];
                        } else {
                            // Tab semua, ambil semua trip
                            trips = Array.isArray(this.detailVehicle) ? this.detailVehicle : [];
                        }

                        let berhenti = 0,
                            meter = 0,
                            mengemudi = 0,
                            idle = 0,
                            starter = 0;
                        let berhentiSec = 0,
                            mengemudiSec = 0,
                            starterSec = 0;

                        trips.forEach(trip => {
                            // event_stop: trip_distance == 0, jumlahkan trip_duration_seconds
                            if (parseFloat(trip.trip_distance) === 0 && trip.trip_duration_seconds) {
                                berhentiSec += parseInt(trip.trip_duration_seconds) || 0;
                            }
                            // event_drive: trip_distance > 0, jumlahkan trip_duration_seconds
                            if (parseFloat(trip.trip_distance) > 0 && trip.trip_duration_seconds) {
                                mengemudiSec += parseInt(trip.trip_duration_seconds) || 0;
                            }
                            // event_starter: semua trip, jumlahkan trip_duration_seconds
                            if (trip.trip_duration_seconds) {
                                starterSec += parseInt(trip.trip_duration_seconds) || 0;
                            }
                            // meter (trip_distance dalam km, konversi ke meter)
                            if (trip.trip_distance) meter += (parseFloat(trip.trip_distance) || 0) * 1000;
                            // idle
                            if (trip.idle_time_seconds) idle += parseInt(trip.idle_time_seconds) || 0;
                        });

                        // Format meter ke kilometer dengan pemisah ribuan
                        let kilometerStr = (meter / 1000).toLocaleString('id-ID', {
                            minimumFractionDigits: 3,
                            maximumFractionDigits: 3
                        });

                        return {
                            berhenti: this.formatSeconds(berhentiSec),
                            kilometer: kilometerStr,
                            mengemudi: this.formatSeconds(mengemudiSec),
                            idle: this.formatSeconds(idle),
                            starter: this.formatSeconds(starterSec)
                        };
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

                    async getYears() {
                        const res = await fetch('/api/project-years');
                        this.years = await res.json();
                    },

                    initPageCartrack() {
                        this.getYears();
                        this.initMap();

                        // inisialisasi flatpickr
                        flatpickr("#dateRangeInput", {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            defaultDate: [
                                this.startDate,
                                this.endDate
                            ],
                            onChange: (selectedDates, dateStr, instance) => {
                                // Kalau sudah pilih 2 tanggal (start & end)
                                if (selectedDates.length === 2) {
                                    const start = selectedDates[0];
                                    const end = selectedDates[1];

                                    // Hitung selisih hari
                                    const diffTime = Math.abs(end - start);
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                                    // Validasi maksimal 9 hari
                                    if (diffDays > 9) {
                                        alert('Rentang tanggal maksimal 9 hari!');

                                        // Reset ke default (7 hari terakhir)
                                        const defaultEnd = new Date();
                                        const defaultStart = new Date();
                                        defaultStart.setDate(defaultStart.getDate() - 7);

                                        instance.setDate([defaultStart, defaultEnd]);
                                        return;
                                    }

                                    // Update properti startDate dan endDate
                                    this.startDate = start;
                                    this.endDate = end;

                                    const startDate = start.toISOString().split("T")[0];
                                    const endDate = end.toISOString().split("T")[0];

                                    console.log(`Range berubah: ${startDate} - ${endDate}`);

                                    // Jika ada vehicle yang sedang aktif, refresh detailnya
                                    if (this.currentVehicle) {
                                        this.showDetail(this.currentVehicle);
                                    }
                                }
                            }
                        });
                        this.init();
                    },

                    initMap() {
                        this.map = L.map('map').setView([-6.733742, 108.530256], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                        }).addTo(this.map);

                        // this.loadData();
                        this.loadDataProjects();
                    },

                    async loadDataProjects() {
                        this.detailVehicle = null;
                        this.currentVehicle = null;
                        this.searchQueryProject = ''; // Reset search
                        await this.searchProjects(); // Gunakan searchProjects untuk load data
                    },

                    async loadData() {
                        this.isLoading = true;

                        // Hapus SEMUA marker (proyek dan kendaraan)
                        this.map.eachLayer((layer) => {
                            if (layer instanceof L.Marker) {
                                this.map.removeLayer(layer);
                            }
                        });

                        // Hapus semua polyline
                        for (const vehicleId in this.polylines) {
                            if (this.polylines[vehicleId]) this.polylines[vehicleId].remove();
                        }
                        this.polylines = {};

                        try {
                            const res = await fetch('/api/cartrack-vehicles');
                            this.vehicles = await res.json();

                            this.vehicles.forEach(v => {
                                if (!v.latest_activity) return;

                                const lastLat = parseFloat(v.latest_activity.end_coordinates_latitude ?? v
                                    .latest_activity
                                    .start_coordinates_latitude);
                                const lastLon = parseFloat(v.latest_activity.end_coordinates_longitude ?? v
                                    .latest_activity
                                    .start_coordinates_longitude);

                                // Buat marker baru setiap kali load
                                this.markers[v.vehicle_id] = L.marker([lastLat, lastLon], {
                                        icon: this.getCarIcon(v.latest_activity.events_idle)
                                    }).addTo(this.map)
                                    .bindPopup(
                                        `<b>${v.heavy_equipment.length > 0 ? v.heavy_equipment[0].name : v.manufacturer + ' ' +v.model + ' ' + v.model_year + ' ' + v.colour}</b><br>Lat: ${lastLat}<br>Lon: ${lastLon}`
                                    )
                                    .on('click', () => {
                                        this.showDetail(v);
                                    });
                            });
                        } catch (error) {
                            console.error('Error loading vehicles:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async showDetail(vehicle) {
                        this.destroyVehicleChart();

                        this.currentVehicle = vehicle;
                        this.tab = 'perhari';

                        this.isChartLoading = true;

                        const formatDate = (date) => {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        };

                        const params = {
                            startDate: formatDate(new Date(this.startDate)),
                            endDate: formatDate(new Date(this.endDate)),
                        };

                        try {
                            // Fetch activities first
                            const res = await fetch('/api/cartrack-activities', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    vehicleId: vehicle.vehicle_id,
                                    ...params
                                })
                            });
                            const json = await res.json();

                            // === HANDLE ERROR RESPONSE ===
                            if (json && json.message === "No activities found.") {
                                this.detailVehicle = [];
                                console.warn("No activities found for this vehicle.");
                            } else {
                                // Normalize: if API returns { data: [...] } use json.data
                                this.detailVehicle = Array.isArray(json) ? json : (json.data || []);
                            }

                            // Normalize: if API returns { data: [...] } use json.data
                            this.detailVehicle = Array.isArray(json) ? json : (json.data || []);

                            // Fetch cartrack details in parallel (wait all)
                            await Promise.all([
                                this.fetchStatusData(vehicle.registration),
                                // this.fetchFuelData(vehicle.registration),
                                // this.fetchBatteryData(vehicle.registration),
                                this.fetchPTOData(vehicle.registration),
                            ]);

                            // Now generateDayList and initialize charts (DOM available)
                            this.generateDayList();

                        } catch (error) {
                            console.error('Error in showDetail:', error);
                        } finally {
                            this.isChartLoading = false;
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.destroyVehicleChart();
                                    this.initPerhariListCharts();

                                    const ptoCanvas = document.getElementById('ptoDetailChart');
                                    if (ptoCanvas) {
                                        this.initPTODetailChart();
                                    }
                                    const batteryCanvas = document.getElementById('batteryEventChart');
                                    if (batteryCanvas) {
                                        this.initBatteryEventChart();
                                    }
                                    const fuelCanvas = document.getElementById('fuelEventChart');
                                    if (fuelCanvas) {
                                        this.initFuelEventChart();
                                    }

                                    this.drawVehiclePolyline(this.detailVehicle);
                                }, 50);
                            });
                        }
                    },

                    drawVehiclePolyline(trips) {
                        // Hapus semua polyline kendaraan dari map
                        Object.values(this.polylines).forEach(polyline => {
                            if (polyline) polyline.remove();
                        });
                        this.polylines = {};

                        // Buat polyline baru dari trip positions
                        const positions = trips
                            .filter(trip => trip.start_coordinates_latitude && trip.start_coordinates_longitude)
                            .map(trip => [
                                parseFloat(trip.start_coordinates_latitude),
                                parseFloat(trip.start_coordinates_longitude)
                            ]);

                        if (positions.length > 1 && this.currentVehicle) {
                            const polyline = L.polyline(positions, {
                                color: this.getColorForVehicle(this.currentVehicle.vehicle_id)
                            }).addTo(this.map);
                            this.polylines[this.currentVehicle.vehicle_id] = polyline;
                            this.map.fitBounds(polyline.getBounds());
                        }
                    },

                    async fetchPTOData(registration) {
                        try {
                            // Format tanggal untuk API Cartrack
                            const formatCartrackDate = (date) => {
                                const d = new Date(date);
                                const year = d.getFullYear();
                                const month = String(d.getMonth() + 1).padStart(2, '0');
                                const day = String(d.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            };

                            const startDate = formatCartrackDate(this.startDate) + ' 00:00:00';
                            const endDate = formatCartrackDate(this.endDate) + ' 23:59:59';

                            console.log('Fetching PTO data for:', registration, 'from', startDate, 'to', endDate);

                            let allPTOData = [];
                            let currentPage = 1;
                            let lastPage = 1;

                            // Loop untuk fetch semua page
                            do {
                                const response = await fetch(
                                    `https://fleetapi-id.cartrack.com/rest/vehicles/${registration}/power-takeoff?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}&page=${currentPage}&limit=500`, {
                                        method: 'GET',
                                        headers: {
                                            'Authorization': 'Basic T1BFUjAwMDE5OmU5MTEzNzc2Y2ZjZDZhN2Q5OTAxYWI5NGU1NWRjY2MyYzU4MjU4Zjg4N2RlNTc0ZTg0MmFjZGQ4YmM2NDAwOWU=',
                                            'Content-Type': 'application/json',
                                        }
                                    }
                                );

                                if (!response.ok) {
                                    console.error(`PTO API error! status: ${response.status}`);
                                    this.ptoRawData = [];
                                    return null;
                                }

                                const ptoData = await response.json();
                                console.log(`PTO data page ${currentPage} received:`, ptoData);

                                // Tambahkan data dari page ini
                                if (ptoData.data && ptoData.data.length > 0) {
                                    allPTOData = allPTOData.concat(ptoData.data);
                                }

                                // Update lastPage dari meta
                                if (ptoData.meta && ptoData.meta.last_page) {
                                    lastPage = ptoData.meta.last_page;
                                }

                                currentPage++;
                            } while (currentPage <= lastPage);

                            console.log('Total PTO data fetched:', allPTOData.length);

                            // Simpan data PTO untuk digunakan di chart
                            this.ptoRawData = allPTOData;

                            return {
                                data: allPTOData,
                                total: allPTOData.length
                            };
                        } catch (error) {
                            console.error('Error fetching PTO data:', error);
                            this.ptoRawData = [];
                            return null;
                        }
                    },

                    async fetchStatusData(registration) {
                        const formatDate = (date) => {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        };

                        const params = {
                            startDate: formatDate(new Date(this.startDate)),
                            endDate: formatDate(new Date(this.endDate)),
                        };

                        try {
                            const res = await fetch('/api/cartrack-statuses', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    registration: registration,
                                    ...params
                                })
                            });
                            const json = await res.json();

                            // === HANDLE ERROR RESPONSE ===
                            if (json && json.status === false) {
                                this.batteryRawData = [];
                                this.fuelRawData = [];
                                console.warn("No activities found for this vehicle.");
                                return null;
                            } else {
                                // Normalize: if API returns { data: [...] } use json.data
                                this.batteryRawData = Array.isArray(json) ? json : (json.data || []);
                                this.fuelRawData = Array.isArray(json) ? json : (json.data || []);
                            }


                        } catch (error) {
                            console.error('Error in fetchStatusData:', error);
                            return null;
                        }
                    },

                    async fetchFuelData(registration) {
                        try {
                            // Format tanggal untuk API Cartrack
                            const formatCartrackDate = (date) => {
                                const d = new Date(date);
                                const year = d.getFullYear();
                                const month = String(d.getMonth() + 1).padStart(2, '0');
                                const day = String(d.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            };

                            const startTimestamp = formatCartrackDate(this.startDate) + ' 00:00:00';
                            const endTimestamp = formatCartrackDate(this.endDate) + ' 23:59:59';

                            console.log('Fetching Fuel data for:', registration, 'from', startTimestamp, 'to',
                                endTimestamp);

                            let allFuelData = [];
                            let currentPage = 1;
                            let lastPage = 1;

                            // Loop untuk fetch semua page
                            do {
                                const response = await fetch(
                                    `https://fleetapi-id.cartrack.com/rest/fuel/fills/${registration}?start_timestamp=${encodeURIComponent(startTimestamp)}&end_timestamp=${encodeURIComponent(endTimestamp)}&page=${currentPage}&per_page=100`, {
                                        method: 'GET',
                                        headers: {
                                            'Authorization': 'Basic T1BFUjAwMDE5OmU5MTEzNzc2Y2ZjZDZhN2Q5OTAxYWI5NGU1NWRjY2MyYzU4MjU4Zjg4N2RlNTc0ZTg0MmFjZGQ4YmM2NDAwOWU=',
                                            'Content-Type': 'application/json',
                                        }
                                    }
                                );

                                if (!response.ok) {
                                    console.error(`Fuel API error! status: ${response.status}`);
                                    this.fuelRawData = [];
                                    return null;
                                }

                                const fuelData = await response.json();
                                console.log(`Fuel data page ${currentPage} received:`, fuelData);

                                // Tambahkan data dari page ini
                                if (fuelData.data && fuelData.data.length > 0) {
                                    allFuelData = allFuelData.concat(fuelData.data);
                                }

                                // Update lastPage dari meta
                                if (fuelData.meta && fuelData.meta.last_page) {
                                    lastPage = fuelData.meta.last_page;
                                }

                                currentPage++;
                            } while (currentPage <= lastPage);

                            console.log('Total fuel data fetched:', allFuelData.length);

                            // Simpan data Fuel untuk digunakan di chart
                            this.fuelRawData = allFuelData;

                            return {
                                data: allFuelData,
                                total: allFuelData.length
                            };
                        } catch (error) {
                            console.error('Error fetching Fuel data:', error);
                            this.fuelRawData = [];
                            return null;
                        }
                    },

                    async fetchBatteryData(registration) {
                        const formatDate = (date) => {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        };

                        const params = {
                            startDate: formatDate(new Date(this.startDate)),
                            endDate: formatDate(new Date(this.endDate)),
                        };

                        try {
                            const response = await fetch(
                                `https://fleetapi-id.cartrack.com/rest/vehicles/battery?filter[registration]=${registration}`, {
                                    method: 'GET',
                                    headers: {
                                        'Authorization': 'Basic T1BFUjAwMDE5OmU5MTEzNzc2Y2ZjZDZhN2Q5OTAxYWI5NGU1NWRjY2MyYzU4MjU4Zjg4N2RlNTc0ZTg0MmFjZGQ4YmM2NDAwOWU=',
                                        'Content-Type': 'application/json',
                                    }
                                }
                            );

                            if (!response.ok) {
                                // Jika status bukan 200, handle error
                                console.error(`Battery API error! status: ${response.status}`);
                                this.batteryRawData = [];
                                return null;
                            }

                            const batteryData = await response.json();
                            this.batteryRawData = batteryData.data || [];
                            return {
                                data: batteryData.data || [],
                                total: batteryData.data ? batteryData.data.length : 0
                            };
                        } catch (error) {
                            console.error('Error fetching Battery data:', error);
                            this.batteryRawData = [];
                            return null;
                        }
                    },

                    formatLatLon(v) {
                        if (!v.latest_activity) return "";

                        return ` ${v.latest_activity.end_location}`;
                    },

                    getPopupContenForProject(project) {
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
                    },

                    getProjectIcon(status) {
                        if (status === 'Selesai') {
                            return this.getCompletedIcon();
                        } else {
                            return this.getOngoingIcon();
                        }
                    },

                    getCompletedIcon() {
                        return L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div class='marker-pin completed'></div><i class='fas fa-check'></i>",
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        });
                    },

                    getOngoingIcon() {
                        return L.divIcon({
                            className: 'custom-div-icon',
                            html: "<div class='marker-pin ongoing'></div><i class='fas fa-clock'></i>",
                            iconSize: [30, 42],
                            iconAnchor: [15, 42]
                        });
                    },

                    getCarIcon(idleStatus = null) {
                        if (idleStatus > 0) {
                            return L.divIcon({
                                className: 'custom-div-icon',
                                html: "<div class='marker-pin completed'></div><i class='fas fa-check'></i>",
                                iconSize: [30, 42],
                                iconAnchor: [15, 42]
                            });
                        } else {
                            return L.divIcon({
                                className: 'custom-div-icon',
                                html: "<div class='marker-pin ongoing'></div><i class='fas fa-clock'></i>",
                                iconSize: [30, 42],
                                iconAnchor: [15, 42]
                            });
                        }
                    },

                    getColorForVehicle(vehicleId) {
                        const colors = [
                            "blue", "red", "green", "orange", "purple",
                            "brown", "pink", "black", "teal", "cyan"
                        ];
                        return colors[vehicleId % colors.length];
                    },

                    getOngoingTrips() {
                        if (!this.detailVehicle) return [];

                        // Untuk testing: tampilkan semua trip dalam range tanggal yang dipilih
                        console.log('All trips in date range:', this.detailVehicle);
                        return this.detailVehicle;

                        // Atau jika ingin filter trip dengan jarak > 0 (yang benar-benar bergerak)
                        // return this.detailVehicle.filter(trip => {
                        //     return parseFloat(trip.trip_distance) > 0;
                        // });
                    },

                    getTotalDistance(trips) {
                        if (!trips || trips.length === 0) return 0;
                        const total = trips.reduce((sum, trip) => {
                            const distance = parseFloat(trip.trip_distance) || 0;
                            console.log(`Trip ${trip.trip_id}: distance = ${distance}`);
                            return sum + distance;
                        }, 0);
                        console.log(`Total distance: ${total}`);
                        return total.toFixed(2);
                    },

                    getTotalDuration(trips) {
                        if (!trips || trips.length === 0) return '0 menit';

                        let totalSeconds = 0;
                        trips.forEach(trip => {
                            if (trip.trip_duration_seconds) {
                                const seconds = parseInt(trip.trip_duration_seconds) || 0;
                                console.log(`Trip ${trip.trip_id}: duration = ${seconds} seconds`);
                                totalSeconds += seconds;
                            }
                        });

                        console.log(`Total seconds: ${totalSeconds}`);
                        const hours = Math.floor(totalSeconds / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);

                        if (hours > 0) {
                            return `${hours} jam ${minutes} menit`;
                        }
                        return `${minutes} menit`;
                    },

                    formatDateTime(createdAt) {
                        const date = new Date(createdAt);
                        const now = new Date();

                        // Normalisasi (mengabaikan jam, menit)
                        const startOfDay = d => new Date(d.getFullYear(), d.getMonth(), d.getDate());

                        const d1 = startOfDay(date);
                        const d2 = startOfDay(now);

                        const diffTime = d2 - d1;
                        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                        const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

                        if (diffDays === 0) return "Hari ini";
                        if (diffDays === 1) return "Kemarin";
                        if (diffDays <= 7) {
                            const dayName = hari[date.getDay()];
                            const tgl = date.getDate().toString().padStart(2, "0");
                            const bln = (date.getMonth() + 1).toString().padStart(2, "0");
                            const thn = date.getFullYear();

                            return `${dayName} (${tgl}/${bln}/${thn})`;
                        }

                        // Lebih dari seminggu → fallback normal
                        return date.toLocaleDateString("id-ID");
                    },

                    destroyPerhariListCharts() {
                        Object.keys(this.perhariListCharts || {}).forEach(key => {
                            try {
                                this.perhariListCharts[key].destroy();
                            } catch (e) {
                                /* ignore */
                            }
                        });
                        this.perhariListCharts = {};
                    },

                    initPerhariListCharts() {
                        // Cleanup first
                        this.destroyPerhariListCharts();

                        if (!Array.isArray(this.dayList) || this.dayList.length === 0) return;

                        // For each day in dayList, create a mini chart in the li canvas
                        this.dayList.forEach(day => {
                            const canvasId = `perhariDurationChart-${day.date}`;
                            const canvas = document.getElementById(canvasId);
                            if (!canvas) return;

                            const ctx = canvas.getContext('2d');

                            // Collect day-specific trips
                            const trips = Array.isArray(this.detailVehicle) ? this.detailVehicle.filter(t => t
                                .start_timestamp && t.start_timestamp.startsWith(day.date)) : [];
                            const labels = trips.map(t => {
                                const start = new Date(t.start_timestamp);
                                return start.toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            });
                            const data = trips.map(t => {
                                if (t.trip_duration_seconds) return Number((parseInt(t.trip_duration_seconds ||
                                    0) / 60).toFixed(1));
                                if (t.start_timestamp && t.end_timestamp) {
                                    const diff = Math.max(0, Math.floor((new Date(t.end_timestamp) - new Date(t
                                        .start_timestamp)) / 1000));
                                    return Number((diff / 60).toFixed(1));
                                }
                                return 0;
                            });

                            // === Tambahkan pengecekan data kosong ===
                            if (!labels.length) {
                                labels.push('Tidak ada data');
                                data.push(0);
                            }

                            // fallback if no trips - show a single zero point or skip
                            const hasData = data.length > 0 && data.some(v => v > 0);

                            try {
                                this.perhariListCharts[day.date] = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: hasData ? labels : ['-'],
                                        datasets: [{
                                            label: 'Duration (minutes)',
                                            data: hasData ? data : [0],
                                            borderColor: '#2563EB',
                                            backgroundColor: 'rgba(37,99,235,0.08)',
                                            tension: 0.3,
                                            fill: true,
                                            pointRadius: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 5
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        },
                                        elements: {
                                            point: {
                                                radius: hasData ? 3 : 0
                                            }
                                        }
                                    }
                                });
                            } catch (err) {
                                console.error('initPerhariListCharts error for', day.date, err);
                            }
                        });
                    },

                    showDetailForDay(dateKey) {
                        // Filter detailVehicle by date dan show single-day mode on the main chart
                        this.isChartLoading = true;
                        const trips = Array.isArray(this.detailVehicle) ? this.detailVehicle.filter(a => a.start_timestamp && a
                            .start_timestamp.startsWith(dateKey)) : [];
                        this._singleDayTrips = trips;
                        this.$nextTick(() => {
                            setTimeout(() => {
                                const batteryCanvas = document.getElementById('batteryEventChart');
                                if (batteryCanvas) this.initBatteryEventChart(dateKey);
                                const fuelCanvas = document.getElementById('fuelEventChart');
                                if (fuelCanvas) this.initFuelEventChart(dateKey);
                                const ptoCanvas = document.getElementById('ptoDetailChart');
                                if (ptoCanvas) this.initPTODetailChart(dateKey);
                                this.isChartLoading = false;
                            }, 50);
                        });
                    },

                    generateDayList() {
                        const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                        const start = new Date(this.startDate);
                        const end = new Date(this.endDate);

                        const toKey = d => d.toISOString().split("T")[0];

                        let loop = new Date(start);
                        let result = [];

                        while (loop <= end) {
                            const key = toKey(loop);

                            // Safety: if detailVehicle not array, skip
                            const dayActivities = Array.isArray(this.detailVehicle) ? this.detailVehicle.filter(a => a
                                .start_timestamp && a.start_timestamp.startsWith(key)) : [];

                            // SUM berbagai field
                            const totalDistance = dayActivities.reduce((acc, a) => acc + (parseFloat(a.trip_distance) || 0), 0);
                            const totalIdleSeconds = dayActivities.reduce((acc, a) => acc + (parseInt(a.idle_time_seconds) ||
                                0), 0);
                            const totalTrips = dayActivities.length;

                            // compute totalDurationSeconds: prefer trip_duration_seconds; otherwise compute end-start
                            const totalDurationSeconds = dayActivities.reduce((acc, a) => {
                                let dur = 0;
                                if (a.trip_duration_seconds) {
                                    dur = parseInt(a.trip_duration_seconds) || 0;
                                } else if (a.start_timestamp && a.end_timestamp) {
                                    const s = new Date(a.start_timestamp);
                                    const e = new Date(a.end_timestamp);
                                    dur = Math.max(0, Math.floor((e - s) / 1000));
                                }
                                return acc + dur;
                            }, 0);

                            // Label hari
                            let label = "";
                            const keyToday = toKey(new Date());
                            const yesterday = new Date();
                            yesterday.setDate(yesterday.getDate() - 1);
                            const keyYesterday = toKey(yesterday);

                            if (key === keyToday) {
                                label = "Hari ini";
                            } else if (key === keyYesterday) {
                                label = "Kemarin";
                            } else {
                                const tgl = loop.getDate().toString().padStart(2, "0");
                                const bln = (loop.getMonth() + 1).toString().padStart(2, "0");
                                const thn = loop.getFullYear();
                                label = `${hari[loop.getDay()]} (${tgl}/${bln}/${thn})`;
                            }

                            result.push({
                                date: key,
                                label: label,
                                totalDistance,
                                totalIdleSeconds,
                                totalTrips,
                                totalDurationSeconds
                            });

                            loop.setDate(loop.getDate() + 1);
                        }

                        this.dayList = result; // terbaru di atas
                    },

                    initBatteryEventChart(dateKey = null) {
                        const canvas = document.getElementById('batteryEventChart');
                        if (!canvas) return;
                        if (this.batteryEventChart) this.batteryEventChart.destroy();

                        let dataArr = this.batteryRawData || [];
                        if (dateKey) {
                            dataArr = dataArr.filter(b => b.event_ts && b.event_ts.startsWith(dateKey));
                        }

                        const labels = (this.batteryRawData || []).map(b =>
                            new Date(b.event_ts).toLocaleString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        );
                        const data = (this.batteryRawData || []).map(b => b.vext);

                        if (!labels.length) {
                            labels.push('Tidak ada data');
                            data.push(0);
                        }

                        try {
                            this.batteryEventChart = new Chart(canvas.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'Battery (%)',
                                        data,
                                        borderColor: '#3B82F6',
                                        backgroundColor: 'rgba(59,130,246,0.08)',
                                        tension: 0.3,
                                        fill: false,
                                        pointRadius: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'nearest',
                                        intersect: false
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            labels: {
                                                usePointStyle: true,
                                                pointStyle: 'circle'
                                            }
                                        },
                                        tooltip: {
                                            enabled: true
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            title: {
                                                display: true,
                                                text: 'Battery (%)'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Timestamp'
                                            },
                                            ticks: {
                                                maxRotation: 45,
                                                minRotation: 45
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('initBatteryEventChart error', err);
                        }
                    },

                    initFuelEventChart(dateKey = null) {
                        const canvas = document.getElementById('fuelEventChart');
                        if (!canvas) return;
                        if (this.fuelEventChart) this.fuelEventChart.destroy();

                        let dataArr = this.fuelRawData || [];
                        if (dateKey) {
                            dataArr = dataArr.filter(f => f.event_ts && f.event_ts.startsWith(dateKey));
                        }

                        const labels = dataArr.map(f =>
                            new Date(f.event_ts).toLocaleString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        );
                        const data = dataArr.map(f => f.fuel_level);

                        if (!labels.length) {
                            labels.push('Tidak ada data');
                            data.push(0);
                        }

                        try {
                            this.fuelEventChart = new Chart(canvas.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'Fuel Fill (litres)',
                                        data,
                                        borderColor: '#F59E0B',
                                        backgroundColor: 'rgba(245,158,11,0.08)',
                                        tension: 0.3,
                                        fill: false,
                                        pointRadius: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'nearest',
                                        intersect: false
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            labels: {
                                                usePointStyle: true,
                                                pointStyle: 'circle'
                                            }
                                        },
                                        tooltip: {
                                            enabled: true
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Litres'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Timestamp'
                                            },
                                            ticks: {
                                                maxRotation: 45,
                                                minRotation: 45
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (err) {
                            console.error('initFuelEventChart error', err);
                        }
                    },

                    initPTODetailChart(dateKey = null) {
                        const canvas = document.getElementById('ptoDetailChart');
                        if (!canvas) return;
                        if (this.ptoDetailChart) this.ptoDetailChart.destroy();

                        let ptoEvents = [];
                        let labels = [];
                        let dataStatus = [];

                        // Filter data sesuai hari yang dipilih
                        if (dateKey) {
                            ptoEvents = (this.ptoRawData || []).filter(p => p.event_time && p.event_time.startsWith(dateKey));
                        } else {
                            ptoEvents = (this.ptoRawData || []);
                        }

                        // Sort by waktu
                        ptoEvents = ptoEvents.slice().sort((a, b) => new Date(a.event_time) - new Date(b.event_time));

                        // Build labels & data (Y: "Active" atau "Deactive")
                        ptoEvents.forEach(p => {
                            const waktu = new Date(p.event_time);
                            labels.push(waktu.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                day: '2-digit',
                                month: 'short'
                            }));
                            dataStatus.push(p.status === 'Active' ? 'Active' : 'Deactive');
                        });

                        // Konversi status ke angka untuk chart (Active=1, Deactive=0)
                        const statusMap = {
                            'Deactive': 0,
                            'Active': 1
                        };

                        if (!labels.length) {
                            labels.push('Tidak ada data');
                            dataStatus.push(0);
                        }

                        try {
                            this.ptoDetailChart = new Chart(canvas.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'PTO Status',
                                        data: dataStatus.map(s => statusMap[s]),
                                        borderColor: '#10B981',
                                        backgroundColor: 'rgba(16,185,129,0.08)',
                                        fill: false,
                                        tension: 0.3,
                                        pointRadius: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,

                                    interaction: {
                                        mode: 'nearest',
                                        intersect: false
                                    },

                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            labels: {
                                                usePointStyle: true,
                                                pointStyle: 'circle'
                                            }
                                        },
                                        tooltip: {
                                            enabled: true
                                        }
                                    },

                                    scales: {
                                        y: {
                                            min: 0,
                                            max: 1,
                                            title: {
                                                display: true,
                                                text: 'Status'
                                            },
                                            ticks: {
                                                callback: function(value) {
                                                    return value === 1 ? 'Active' : 'Deactive';
                                                },
                                                stepSize: 1
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Waktu'
                                            },
                                            ticks: {
                                                maxRotation: 45,
                                                minRotation: 45
                                            }
                                        }
                                    }
                                }

                            });
                        } catch (err) {
                            console.error('initPTODetailChart error', err);
                        }
                    },

                    formatSeconds(seconds) {
                        const h = Math.floor(seconds / 3600).toString().padStart(2, "0");
                        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, "0");
                        const s = Math.floor(seconds % 60).toString().padStart(2, "0");
                        return `${h}:${m}:${s}`;
                    }
                }
            }
        </script>
    @endpush
</x-cartrack-layout>

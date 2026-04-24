<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- Tambahkan ini di bagian atas body, setelah opening tag dari div dengan class py-12 -->
        <div id="notification" class="fixed top-0 right-0 m-6 p-4 rounded shadow-lg hidden transition-opacity duration-300 ease-in-out">
            <p id="notification-message"></p>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">There were some problems with your input.</span>
                    <ul class="mt-3 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('work-assignments.update', $workAssignment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="page" value="{{ request()->query('page') }}">
                        <input type="hidden" name="year" value="{{ request()->query('year') }}">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="project_name" :value="__('Nama Pekerjaan')" required />
                                <x-text-input id="project_name" class="block mt-1 w-full" type="text" name="project_name" :value="old('project_name', $workAssignment->project_name)" required />
                            </div>
                            <div>
                                <x-input-label for="nomor_lambung" :value="__('Nomor Lambung')" required />
                                <select id="nomor_lambung" name="nomor_lambung" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Nomor Lambung</option>
                                    @foreach($heavyEquipments as $equipment)
                                        @if ($equipment->status == 'ready' || $equipment->id == $workAssignment->heavy_equipment_id)
                                            <option value="{{ $equipment->id }}" {{ $equipment->id == $workAssignment->heavy_equipment_id ? 'selected' : '' }}>
                                                {{ $equipment->nomor_lambung }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="heavy_equipment_id" :value="__('Alat yang Digunakan')" />
                                <input type="text" id="heavy_equipment_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $workAssignment->heavyEquipment->name }}" readonly>
                                <input type="hidden" id="heavy_equipment_id" name="heavy_equipment_id" value="{{ $workAssignment->heavy_equipment_id }}">
                            </div>

                            <div>
                                <x-input-label for="expected_duration" :value="__('Jumlah Hari Pekerjaan')" required />
                                <x-text-input id="expected_duration" class="block mt-1 w-full" type="number" name="expected_duration" :value="old('expected_duration', $workAssignment->expected_duration)" required />
                            </div>

                            <!-- Operator Section -->
                            <div>
                                <x-input-label for="operator_id" :value="__('Operator')" class="text-lg font-semibold mb-2" />
                                <div class="bg-gray-100 p-4 rounded-lg">
                                    <h4 class="text-md font-medium mb-2">Operator Saat Ini</h4>
                                    <ul id="operator-list" class="space-y-2">
                                        @if($workAssignment->assignmentUsers->where('role', 'operator')->count() > 0)
                                            @foreach($workAssignment->assignmentUsers->where('role', 'operator') as $assignmentUser)
                                                <li class="flex items-center justify-between bg-white p-2 rounded" data-id="{{ $assignmentUser->id }}">
                                                    <span>
                                                        {{ $assignmentUser->user->name }}
                                                        <br>
                                                        <span class="text-sm text-gray-600">
                                                            ({{ $assignmentUser->start_date }} - {{ $assignmentUser->end_date }})
                                                        </span>
                                                    </span>
                                                    <button type="button" 
                                                            class="remove-user-btn text-red-600 hover:text-red-800"
                                                            data-action="remove-user" 
                                                            data-id="{{ $assignmentUser->id }}" 
                                                            data-role="operator">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </li>
                                            @endforeach
                                        @else
                                            <p class="text-gray-500">Belum ada operator yang ditugaskan.</p>
                                        @endif
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <select id="operator_id" name="operator_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Pilih Operator</option>
                                        @foreach($availableUsers as $user)
                                            @if ($user->hasRole('operator') && $user->status === 'tersedia')
                                                <option value="{{ $user->id }}" data-roles="{{ json_encode($user->roles) }}">{{ $user->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-2 sm:flex sm:space-x-2">
                                    <div class="w-full sm:w-1/2 mt-2">
                                        <label for="operator_start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                        <input type="date" 
                                               id="operator_start_date"
                                               name="operator_start_date" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div class="w-full sm:w-1/2 mt-2 md:ml-2">
                                        <label for="operator_end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                        <input type="date" 
                                               id="operator_end_date"
                                               name="operator_end_date" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>

                            <!-- Helper Section -->
                            <div>
                                <x-input-label for="helper_id" :value="__('Helper')" class="text-lg font-semibold mb-2" />
                                <div class="bg-gray-100 p-4 rounded-lg">
                                    <h4 class="text-md font-medium mb-2">Helper Saat Ini</h4>
                                    <ul id="helper-list" class="space-y-2">
                                        @if($workAssignment->assignmentUsers->where('role', 'helper')->count() > 0)
                                            @foreach($workAssignment->assignmentUsers->where('role', 'helper') as $assignmentUser)
                                                <li class="flex items-center justify-between bg-white p-2 rounded" data-id="{{ $assignmentUser->id }}">
                                                    <span>
                                                        {{ $assignmentUser->user->name }}
                                                        <br>
                                                        <span class="text-sm text-gray-600">
                                                            ({{ $assignmentUser->start_date }} - {{ $assignmentUser->end_date }})
                                                        </span>
                                                    </span>
                                                    <button type="button" 
                                                            class="remove-user-btn text-red-600 hover:text-red-800"
                                                            data-action="remove-user" 
                                                            data-id="{{ $assignmentUser->id }}" 
                                                            data-role="helper">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </li>
                                            @endforeach
                                        @else
                                            <p class="text-gray-500">Belum ada helper yang ditugaskan.</p>
                                        @endif
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <select id="helper_id" name="helper_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Pilih Helper</option>
                                        @foreach($availableUsers as $user)
                                            @if ($user->hasRole('helper') && $user->status === 'tersedia')
                                                <option value="{{ $user->id }}" data-roles="{{ json_encode($user->roles) }}">{{ $user->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-2 sm:flex sm:space-x-2">
                                    <div class="w-full sm:w-1/2 mt-2">
                                        <label for="helper_start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                        <input type="date" 
                                               id="helper_start_date"
                                               name="helper_start_date" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div class="w-full sm:w-1/2 mt-2 md:ml-2">
                                        <label for="helper_end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                        <input type="date" 
                                               id="helper_end_date"
                                               name="helper_end_date" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" required />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $workAssignment->start_date->format('Y-m-d'))" required />
                            </div>

                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" required />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $workAssignment->end_date->format('Y-m-d'))" required />
                            </div>
                            <div>
                                <x-input-label for="permasalahan" :value="__('Permasalahan')" />
                                <textarea id="permasalahan" name="permasalahan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('permasalahan', $workAssignment->permasalahan) }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="tipe_pekerjaan" :value="__('Tipe Pekerjaan')" required />
                                <x-text-input id="tipe_pekerjaan" class="block mt-1 w-full" type="text" name="tipe_pekerjaan" :value="old('tipe_pekerjaan', $workAssignment->tipe_pekerjaan)" required />
                            </div>

                            <div class="md:col-span-2">
                                <div id="map" class="z-10" style="height: 400px;"></div>
                            </div>

                            <div>
                                <x-input-label for="latitude" :value="__('Latitude')" required />
                                <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="any" name="latitude" :value="old('latitude', $workAssignment->latitude)" required />
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude')" required />
                                <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="any" name="longitude" :value="old('longitude', $workAssignment->longitude)" required />
                            </div>

                            <div>
                                <x-input-label for="city_id" :value="__('Kota/Kabupaten')" required />
                                <select id="city_id" name="city_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->code }}" {{ $city->code == $workAssignment->city_id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="district_id" :value="__('Kecamatan')" required />
                                <select id="district_id" name="district_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="village_id" :value="__('Desa/Kelurahan')" required />
                                <select id="village_id" name="village_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Desa/Kelurahan</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="panjang_penanganan" :value="__('Panjang Penanganan (km)')"  />
                                <x-text-input id="panjang_penanganan" placeholder="Kosongkan jika tidak ada" class="block mt-1 w-full" type="number" step="0.01" name="panjang_penanganan" :value="old('panjang_penanganan', $workAssignment->panjang_penanganan)" />
                            </div>
                            <div>
                                <x-input-label for="alamat" :value="__('Alamat')" />
                                <textarea id="alamat" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" name="alamat">{{ old('alamat', $workAssignment->alamat) }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="documentation_link" :value="__('Link Dokumentasi')" />
                                <textarea id="documentation_link" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" name="documentation_link">{{ old('documentation_link', $workAssignment->documentation_link) }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4" type="submit" color="indigo">
                                {{ __('Update Data Pekerjaan') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal Konfirmasi -->
     <div id="confirmationModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Konfirmasi Penghapusan
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus pengguna ini?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                    <button type="button" id="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
        <script >
            // Data alat berat dalam format JSON
            document.addEventListener('DOMContentLoaded', () => {
                const heavyEquipments = @json($heavyEquipments->map(function($equipment) {
                    return [
                        'id' => $equipment->id,
                        'nomor_lambung' => $equipment->nomor_lambung,
                        'name' => $equipment->name
                    ];
                }));

                const nomorLambungSelect = document.getElementById('nomor_lambung');
                const heavyEquipmentNameInput = document.getElementById('heavy_equipment_name');
                const heavyEquipmentIdInput = document.getElementById('heavy_equipment_id');

                nomorLambungSelect.addEventListener('change', function() {
                    const selectedEquipmentId = this.value;
                    const selectedEquipment = heavyEquipments.find(equipment => equipment.id == selectedEquipmentId);

                    if (selectedEquipment) {
                        heavyEquipmentNameInput.value = selectedEquipment.name;
                        heavyEquipmentIdInput.value = selectedEquipment.id;
                    } else {
                        heavyEquipmentNameInput.value = '';
                        heavyEquipmentIdInput.value = '';
                    }
                });

                var map = L.map('map').setView([{{ $workAssignment->latitude }}, {{ $workAssignment->longitude }}], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© PU Cimancis'
                }).addTo(map);

                var marker = L.marker([{{ $workAssignment->latitude }}, {{ $workAssignment->longitude }}]).addTo(map);

                const citySelect = document.getElementById('city_id');
                const districtSelect = document.getElementById('district_id');
                const villageSelect = document.getElementById('village_id');

                citySelect.addEventListener('change', function() {
                    fetchDistricts(this.value);
                });

                districtSelect.addEventListener('change', function() {
                    fetchVillages(this.value);
                });

                function fetchDistricts(cityId) {
                    return new Promise((resolve) => {
                        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

                        if (cityId) {
                            fetch(`/api/districts/${cityId}`)
                                .then(response => response.json())
                                .then(data => {
                                    data.forEach(district => {
                                        districtSelect.add(new Option(district.name, district.code));
                                    });
                                    districtSelect.value = '{{ $workAssignment->district_id }}';
                                    fetchVillages('{{ $workAssignment->district_id }}');
                                    resolve();
                                });
                        } else {
                            resolve();
                        }
                    });
                }

                function fetchVillages(districtId) {
                    villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

                    if (districtId) {
                        fetch(`/api/villages/${districtId}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(village => {
                                    villageSelect.add(new Option(village.name, village.code));
                                });
                                villageSelect.value = '{{ $workAssignment->village_id }}';
                            });
                    }
                }

                function updateLocationInfo(latlng) {
                    document.getElementById('latitude').value = latlng.lat.toFixed(6);
                    document.getElementById('longitude').value = latlng.lng.toFixed(6);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            const city = data.address.city || data.address.town || data.address.county || '';
                            const district = data.address.district || data.address.suburb || '';
                            const village = data.address.village || data.address.hamlet || '';

                            // Update city/regency
                            let cityOption;
                            if (city.toLowerCase().includes('cirebon')) {
                                if (data.address.city) {
                                    cityOption = Array.from(citySelect.options).find(option => option.text.toLowerCase().includes('kota cirebon'));
                                } else if (data.address.county) {
                                    cityOption = Array.from(citySelect.options).find(option => option.text.toLowerCase().includes('kabupaten cirebon'));
                                }
                            } else {
                                cityOption = Array.from(citySelect.options).find(option =>
                                    option.text.toLowerCase().includes(city.toLowerCase())
                                );
                            }

                            if (cityOption) {
                                citySelect.value = cityOption.value;
                                fetchDistricts(cityOption.value).then(() => {
                                    if (district) {
                                        selectDistrict(district).then(() => {
                                            if (village) {
                                                fetchVillagesAndSelectVillage(districtSelect.value, village);
                                            }
                                        });
                                    }
                                });
                            } else {
                                console.log('Matching city not found:', city);
                            }

                            // Update village
                            setTimeout(() => {
                                if (village) {
                                    detectDistrict(citySelect.value, village);
                                } else {
                                    console.log('No matching village found.');
                                }
                            }, 1000);

                            document.getElementById('alamat').value = data.display_name || '';
                        });
                }

                // Event listener untuk klik pada peta
                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    updateLocationInfo(e.latlng);
                });

                // Tambahkan kontrol pencarian
                var geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false
                }).addTo(map);

                geocoder.on('markgeocode', function(e) {
                    var bbox = e.geocode.bbox;
                    var poly = L.polygon([
                        bbox.getSouthEast(),
                        bbox.getNorthEast(),
                        bbox.getNorthWest(),
                        bbox.getSouthWest()
                    ]).addTo(map);
                    map.fitBounds(poly.getBounds());

                    marker.setLatLng(e.geocode.center);
                    updateLocationInfo(e.geocode.center);
                });

                function detectDistrict(cityId, villageName) {
                    fetch(`/api/detect-district?city_id=${cityId}&village_name=${villageName}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            // Pastikan data selalu diperlakukan sebagai array
                            const districts = Array.isArray(data) ? data : [data];

                            // Jika ada data yang ditemukan
                            if (districts.length > 0 && districts[0].code) {
                                const districtCode = districts[0].code;
                                districtSelect.value = districtCode;

                                // Setelah memilih kecamatan, ambil daftar desa dan pilih desa yang sesuai
                                fetchVillagesAndSelectVillage(districtCode, villageName);
                            } else {
                                console.error('No district found');
                            }
                        });
                }

                function fetchVillagesAndSelectVillage(districtCode, villageName) {
                    fetch(`/api/villages/${districtCode}`)
                        .then(response => response.json())
                        .then(data => {
                            villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                            data.forEach(village => {
                                villageSelect.add(new Option(village.name, village.code));
                            });

                            // Coba pilih desa yang sesuai
                            selectVillage(villageName);
                        });
                }

                function selectVillage(villageName) {
                    const villageOption = Array.from(villageSelect.options).find(option =>
                        option.text.toLowerCase().includes(villageName.toLowerCase())
                    );
                    if (villageOption) {
                        villageSelect.value = villageOption.value;
                    } else {
                        console.log('Matching village not found:', villageName);
                    }
                }

                function selectDistrict(districtName) {
                    return new Promise((resolve) => {
                        const districtOption = Array.from(districtSelect.options).find(option =>
                            option.text.toLowerCase().includes(districtName.toLowerCase())
                        );
                        if (districtOption) {
                            districtSelect.value = districtOption.value;
                            fetchVillages(districtOption.value);
                        } else {
                            console.log('Matching district not found:', districtName);
                        }
                        resolve();
                    });
                }

                // Initialize districts and villages
                fetchDistricts('{{ $workAssignment->city_id }}').then(() => {
                    fetchVillages('{{ $workAssignment->district_id }}');
                });

                const modal = document.getElementById('confirmationModal');
                const confirmDeleteBtn = document.getElementById('confirmDelete');
                const cancelDeleteBtn = document.getElementById('cancelDelete');
                let currentAssignmentUserId, currentRole;

                function showModal(assignmentUserId, role) {
                    currentAssignmentUserId = assignmentUserId;
                    currentRole = role;
                    modal.classList.remove('hidden');
                }

                function hideModal() {
                    modal.classList.add('hidden');
                }

                function showNotification(message, type = 'success') {
                    const notification = document.getElementById('notification');
                    const notificationMessage = document.getElementById('notification-message');
                    
                    notificationMessage.textContent = message;
                    
                    // Set warna berdasarkan tipe notifikasi
                    if (type === 'success') {
                        notification.classList.add('bg-green-500', 'text-white');
                        notification.classList.remove('bg-red-500');
                    } else {
                        notification.classList.add('bg-red-500', 'text-white');
                        notification.classList.remove('bg-green-500');
                    }
                    
                    notification.classList.remove('hidden');
                    notification.classList.add('opacity-100');
                    
                    // Sembunyikan notifikasi setelah 3 detik
                    setTimeout(() => {
                        notification.classList.remove('opacity-100');
                        notification.classList.add('opacity-0');
                        setTimeout(() => {
                            notification.classList.add('hidden');
                        }, 300);
                    }, 3000);
                }

                function removeUser(assignmentUserId, role) {
                    fetch(`/admin/work-assignments/remove-user/${assignmentUserId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            
                            // Refresh halaman setelah penghapusan berhasil
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000); // Tunggu 1 detik sebelum refresh agar notifikasi terlihat
                        } else {
                            throw new Error(data.message || 'Gagal menghapus pengguna.');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan: ' + error.message, 'error');
                    })
                    .finally(() => {
                        hideModal();
                    });
                }

                const operatorSelect = document.getElementById('operator_id');
                const helperSelect = document.getElementById('helper_id');
                const operatorStartDate = document.querySelector('input[name="operator_start_date"]');
                const operatorEndDate = document.querySelector('input[name="operator_end_date"]');
                const helperStartDate = document.querySelector('input[name="helper_start_date"]');
                const helperEndDate = document.querySelector('input[name="helper_end_date"]');

                function updateSelections(changedSelect, otherSelect) {
                    const selectedId = changedSelect.value;
                    const selectedOption = changedSelect.options[changedSelect.selectedIndex];
                    const selectedRoles = selectedOption.dataset.roles ? JSON.parse(selectedOption.dataset.roles) : [];

                    Array.from(otherSelect.options).forEach(option => {
                        if (option.value === '') return; // Skip the default "Select" option
                        const optionRoles = JSON.parse(option.dataset.roles);
                        if (selectedId === option.value) {
                            // Disable the same user in the other select
                            option.disabled = true;
                        } else {
                            // Enable options based on their roles
                            option.disabled = false;
                            if (changedSelect === operatorSelect && !optionRoles.includes('helper')) {
                                option.disabled = true;
                            } else if (changedSelect === helperSelect && !optionRoles.includes('operator')) {
                                option.disabled = true;
                            }
                        }
                    });

                    // Disable options that are already assigned
                    document.querySelectorAll('#operator-list li, #helper-list li').forEach(li => {
                        const userId = li.getAttribute('data-user-id');
                        if (userId && userId !== selectedId) {
                            const operatorOption = operatorSelect.querySelector(`option[value="${userId}"]`);
                            if (operatorOption) operatorOption.disabled = true;
                            
                            const helperOption = helperSelect.querySelector(`option[value="${userId}"]`);
                            if (helperOption) helperOption.disabled = true;
                        }
                    });
                }

                function updateDropdowns(changedSelect, otherSelect) {
                    const selectedValue = changedSelect.value;

                    // Re-enable all options in the other dropdown
                    Array.from(otherSelect.options).forEach(option => {
                        option.disabled = false;
                    });

                    // If a value is selected, disable that option in the other dropdown
                    if (selectedValue) {
                        const optionToDisable = otherSelect.querySelector(`option[value="${selectedValue}"]`);
                        if (optionToDisable) {
                            optionToDisable.disabled = true;
                        }
                    }
                }

                operatorSelect.addEventListener('change', () => {
                    updateSelections(operatorSelect, helperSelect);
                    if (operatorSelect.value) {
                        operatorStartDate.required = true;
                        operatorEndDate.required = true;
                    } else {
                        operatorStartDate.required = false;
                        operatorEndDate.required = false;
                    }
                });

                helperSelect.addEventListener('change', () => {
                    updateSelections(helperSelect, operatorSelect);
                    if (helperSelect.value) {
                        helperStartDate.required = true;
                        helperEndDate.required = true;
                    } else {
                        helperStartDate.required = false;
                        helperEndDate.required = false;
                    }
                });

                // Function to add a new user to the list
                function addUserToList(role, userId, userName, startDate, endDate) {
                    const list = document.getElementById(`${role}-list`);
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between bg-white p-2 rounded';
                    li.setAttribute('data-id', `temp-${Date.now()}`); // Temporary ID
                    li.setAttribute('data-user-id', userId);
                    li.innerHTML = `
                        <span>
                            ${userName}
                            <br>
                            <span class="text-sm text-gray-600">
                                (${startDate} - ${endDate})
                            </span>
                        </span>
                        <button type="button" 
                                class="remove-user-btn text-red-600 hover:text-red-800"
                                data-action="remove-user" 
                                data-id="temp-${Date.now()}" 
                                data-role="${role}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    `;
                    list.appendChild(li);
                    
                    // Clear the select and date inputs
                    document.getElementById(`${role}_id`).value = '';
                    document.querySelector(`input[name="${role}_start_date"]`).value = '';
                    document.querySelector(`input[name="${role}_end_date"]`).value = '';

                    // Update selections to reflect the new assignment
                    updateSelections(operatorSelect, helperSelect);
                }
                            // Event listener for form submission
                document.querySelector('form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const operatorId = operatorSelect.value;
                    const helperId = helperSelect.value;

                    if (operatorId && operatorStartDate.value && operatorEndDate.value) {
                        const operatorName = operatorSelect.options[operatorSelect.selectedIndex].text;
                        addUserToList('operator', operatorId, operatorName, operatorStartDate.value, operatorEndDate.value);
                    }

                    if (helperId && helperStartDate.value && helperEndDate.value) {
                        const helperName = helperSelect.options[helperSelect.selectedIndex].text;
                        addUserToList('helper', helperId, helperName, helperStartDate.value, helperEndDate.value);
                    }

                    // Continue with form submission
                    e.target.submit();
                });

                // Initial update of selections
                updateSelections(operatorSelect, helperSelect);
                updateSelections(helperSelect, operatorSelect);

                // Event delegation for dynamically added elements
                document.body.addEventListener('click', function(event) {
                    const target = event.target.closest('.remove-user-btn');
                    if (target) {
                        event.preventDefault();
                        const assignmentUserId = target.getAttribute('data-id');
                        const role = target.getAttribute('data-role');
                        showModal(assignmentUserId, role);
                    }
                });

                confirmDeleteBtn.addEventListener('click', function() {
                    removeUser(currentAssignmentUserId, currentRole);
                });

                cancelDeleteBtn.addEventListener('click', hideModal);

            
                        
            });
        </script>
    @endpush
   
</x-app-layout>

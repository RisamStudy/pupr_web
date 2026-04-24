<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
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
                    <form action="{{ route('work-assignments.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="project_name" :value="__('Nama Pekerjaan')" required />
                                <x-text-input id="project_name" class="block mt-1 w-full" type="text" name="project_name" :value="old('project_name')" required />
                            </div>
                            <div>
                                <x-input-label for="nomor_lambung" :value="__('Nomor Lambung')" required />
                                <select id="nomor_lambung" name="nomor_lambung" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Nomor Lambung</option>
                                    @foreach($heavyEquipments as $equipment)
                                        @if ($equipment->status == 'ready'){
                                            <option value="{{ $equipment->id }}">{{ $equipment->nomor_lambung }}</option>
                                        }
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="heavy_equipment_id" :value="__('Alat yang Digunakan')" />
                                <input type="text" id="heavy_equipment_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Pilih Nomor Lambung" readonly>
                                <input type="hidden" id="heavy_equipment_id" name="heavy_equipment_id">
                            </div>

                            <div>
                                <x-input-label for="expected_duration" :value="__('Jumlah Hari Pekerjaan')" required />
                                <x-text-input id="expected_duration" class="block mt-1 w-full" type="number" name="expected_duration" :value="old('expected_duration')" required />
                            </div>

                            <div>
                                <x-input-label for="operator_id" :value="__('Operator')" required />
                                <select id="operator_id" name="operator_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Operator</option>
                                    @foreach($users as $user)
                                        @if (in_array('operator', $user->roles) && $user->status === 'tersedia')
                                            <option value="{{ $user->id }}" data-roles="{{ json_encode($user->roles) }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="helper_id" :value="__('Helper')" required />
                                <select id="helper_id" name="helper_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Helper</option>
                                    @foreach($users as $user)
                                        @if (in_array('helper', $user->roles) && $user->status === 'tersedia')
                                            <option value="{{ $user->id }}" data-roles="{{ json_encode($user->roles) }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" required />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                            </div>

                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" required />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" required />
                            </div>
                            <div>
                                <x-input-label for="permasalahan" :value="__('Permasalahan')"  />
                                <textarea id="permasalahan" name="permasalahan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('permasalahan') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="tipe_pekerjaan" :value="__('Tipe Pekerjaan')" required />
                                <x-text-input id="tipe_pekerjaan" class="block mt-1 w-full" type="text" name="tipe_pekerjaan" :value="old('tipe_pekerjaan')" required />
                            </div>

                            <div class="md:col-span-2">
                                <div id="map" class="z-10" style="height: 400px;"></div>
                            </div>

                            <div>
                                <x-input-label for="latitude" :value="__('Latitude')" required />
                                <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="any" name="latitude" :value="old('latitude')" required />
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude')" required />
                                <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="any" name="longitude" :value="old('longitude')" required />
                            </div>

                            <div>
                                <x-input-label for="city_id" :value="__('Kota/Kabupaten')" required />
                                <select id="city_id" name="city_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->code }}">{{ $city->name }}</option>
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
                                <x-input-label for="panjang_penanganan" :value="__('Panjang Penanganan (km)')" />
                                <x-text-input id="panjang_penanganan" placeholder="Kosongkan jika tidak ada" class="block mt-1 w-full" type="number" step="0.01" name="panjang_penanganan" :value="old('panjang_penanganan')" />
                            </div>
                            <div>
                                <x-input-label for="alamat" :value="__('Alamat')" />
                                <textarea id="alamat" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" name="alamat">{{ old('alamat') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="documentation_link" :value="__('Link Dokumentasi')" />
                                <textarea id="documentation_link" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" name="documentation_link">{{ old('documentation_link') }}</textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4" type="submit" color="indigo">
                                {{ __('Tambah Data') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const operatorSelect = document.getElementById('operator_id');
            const helperSelect = document.getElementById('helper_id');

            function updateSelections(changedSelect, otherSelect) {
                const selectedId = changedSelect.value;
                const selectedOption = changedSelect.options[changedSelect.selectedIndex];
                const selectedRoles = selectedOption.dataset.roles ? JSON.parse(selectedOption.dataset.roles) : [];

                Array.from(otherSelect.options).forEach(option => {
                    if (option.value === '') return; // Skip the default "Select" option
                    if (selectedId === option.value) {
                        // Disable the same user in the other select
                        option.disabled = true;
                    } else {
                        // Enable all other options
                        option.disabled = false;
                    }
                });

                // If an operator is selected, disable options in helper select that don't have 'helper' role
                if (changedSelect.id === 'operator_id' && selectedId !== '') {
                    Array.from(helperSelect.options).forEach(option => {
                        if (option.value === '') return;
                        const optionRoles = JSON.parse(option.dataset.roles);
                        if (!optionRoles.includes('helper')) {
                            option.disabled = true;
                        }
                    });
                }

                // If a helper is selected, disable options in operator select that don't have 'operator' role
                if (changedSelect.id === 'helper_id' && selectedId !== '') {
                    Array.from(operatorSelect.options).forEach(option => {
                        if (option.value === '') return;
                        const optionRoles = JSON.parse(option.dataset.roles);
                        if (!optionRoles.includes('operator')) {
                            option.disabled = true;
                        }
                    });
                }
            }

            operatorSelect.addEventListener('change', function() {
                updateSelections(operatorSelect, helperSelect);
            });

            helperSelect.addEventListener('change', function() {
                updateSelections(helperSelect, operatorSelect);
            });

            // Initial update
            updateSelections(operatorSelect, helperSelect);
            updateSelections(helperSelect, operatorSelect);

            // Data alat berat dalam format JSON
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
                    heavyEquipmentNameInput.value = selectedEquipment.name + ' - (' + (selectedEquipment.hours_meter ?? 0) + ' HM)';
                    heavyEquipmentIdInput.value = selectedEquipment.id;
                } else {
                    heavyEquipmentNameInput.value = '';
                    heavyEquipmentIdInput.value = '';
                }
            });

            var map = L.map('map').setView([-6.7320229, 108.5523164], 11); // Koordinat Cirebon

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© PU Cimancis'
            }).addTo(map);

            var marker;

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
                        }
                    );
                }
            }

            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            // Fungsi untuk memperbarui peta dan info lokasi berdasarkan input koordinat
            function updateMapFromCoordinates() {
                const lat = parseFloat(latitudeInput.value);
                const lng = parseFloat(longitudeInput.value);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const latlng = L.latLng(lat, lng);

                    if (marker) {
                        map.removeLayer(marker);
                    }
                    marker = L.marker(latlng).addTo(map);
                    map.setView(latlng, 13);

                    updateLocationInfo(latlng);
                }
            }

            // Event listener untuk input koordinat manual
            latitudeInput.addEventListener('change', updateMapFromCoordinates);
            longitudeInput.addEventListener('change', updateMapFromCoordinates);

            // Modifikasi fungsi updateLocationInfo untuk menangani pembaruan otomatis
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
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng).addTo(map);
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

                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.geocode.center).addTo(map);
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
        }
        );
    </script>
</x-app-layout>

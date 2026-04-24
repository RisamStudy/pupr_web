<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Alat Berat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('alat-berat.update', $heavyEquipment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="nomor_lambung" class="block text-sm font-medium text-gray-700">Nomor Lambung
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_lambung" id="nomor_lambung"
                                    value="{{ $heavyEquipment->nomor_lambung }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kendaraan
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ $heavyEquipment->name }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun <span
                                        class="text-red-500">*</span></label>
                                <input type="number" value="{{ $heavyEquipment->tahun }}" name="tahun" id="tahun"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>
                            <div>
                                <label for="merek" class="block text-sm font-medium text-gray-700">Merek <span
                                        class="text-red-500">*</span></label>
                                <input type="text" value="{{ $heavyEquipment->merek }}" name="merek" id="merek"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status <span
                                        class="text-red-500">*</span></label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                    <option value="beroperasi"
                                        {{ $heavyEquipment->status == 'beroperasi' ? 'selected' : '' }}>Beroperasi
                                    </option>
                                    <option value="ready" {{ $heavyEquipment->status == 'ready' ? 'selected' : '' }}>
                                        Ready</option>
                                    <option value="maintenance"
                                        {{ $heavyEquipment->status == 'maintenance' ? 'selected' : '' }}>Maintenance
                                    </option>
                                    <option value="rusak" {{ $heavyEquipment->status == 'rusak' ? 'selected' : '' }}>
                                        Rusak</option>
                                    <option value="tidak ada" {{ $heavyEquipment->status == 'tidak ada' ? 'selected' : '' }}>
                                        Tidak Ada</option>
                                </select>
                            </div>
                            <div>
                                <label for="kondisi" class="block text-sm font-medium text-gray-700">Kondisi <span
                                        class="text-red-500">*</span></label>
                                <select name="kondisi" id="kondisi"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                    <option value="baik" {{ $heavyEquipment->status == 'baik' ? 'selected' : '' }}>
                                        Baik</option>
                                    <option value="rusak_ringan"
                                        {{ $heavyEquipment->status == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan
                                    </option>
                                    <option value="rusak_berat"
                                        {{ $heavyEquipment->status == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Lokasi Kendaraan
                                    <span class="text-red-500">*</span></label>
                                <input type="text" value="{{ $heavyEquipment->location }}" name="location"
                                    id="location"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>
                            <div>
                                <label for="cartrack_vehicles" class="block text-sm font-medium text-gray-700">Cartrack
                                    Vehicles
                                    <span class="text-red-500">*</span></label>
                                <select name="cartrack_vehicles" id="cartrack_vehicles"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                    @forelse ($cartrackVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            {{ $heavyEquipment->cartrack_vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->registration . ' ' . $vehicle->manufacturer }}
                                        </option>
                                    @empty
                                        <option value="">
                                            Tidak Ada Kendaraan</option>
                                    @endforelse
                                </select>
                            </div>
                            <div>
                                <label for="hours_meter" class="block text-sm font-medium text-gray-700">Hours
                                    Meter</label>
                                <input type="text" value="{{ $heavyEquipment->hours_meter }}" name="hours_meter"
                                    id="hours_meter"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Alat Berat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

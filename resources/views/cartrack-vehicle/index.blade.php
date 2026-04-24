<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cartrack Vehicles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <button type="button" id="sync-cartrack"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Sinkronisasi Cartrack
                        </button>
                        <button type="button" id="sync-cartrack-with-heavy-equipment"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Sinkronisasi Cartrack dengan Alat Berat
                        </button>
                    </div>
                    {{-- Table --}}
                    <div class="shadow overflow-hidden overflow-x-auto border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No.</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Registration</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle Name</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Manufaturer</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Model</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Colour</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Chasis Number</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($cartrack_vehicles as $key => $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $key + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->registration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->vehicle_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->manufacturer }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->model . ' ' . $item->model_year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->colour ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->chassis_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="#"
                                                class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat</a>
                                            <a href="#" class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                            <button class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $cartrack_vehicles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const syncCartrack = document.getElementById('sync-cartrack');
                const syncCartrackWithHeavyEquipment = document.getElementById('sync-cartrack-with-heavy-equipment');

                syncCartrack.addEventListener('click', function() {

                    // disabled button
                    syncCartrack.disabled = true;
                    syncCartrackWithHeavyEquipment.disabled = true;
                    syncCartrack.textContent = 'Menyinkronkan...';

                    fetch('/api/sync-cartrack', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            console.log(data);
                            syncCartrack.disabled = false;
                            syncCartrackWithHeavyEquipment.disabled = false;
                            syncCartrack.textContent = 'Sinkronisasi Data';
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menyinkronkan data.');
                            syncCartrack.disabled = false;
                            syncCartrackWithHeavyEquipment.disabled = false;
                            syncCartrack.textContent = 'Sinkronisasi Data';
                        });

                });

                syncCartrackWithHeavyEquipment.addEventListener('click', function() {

                    // disabled button
                    syncCartrack.disabled = true;
                    syncCartrackWithHeavyEquipment.disabled = true;
                    syncCartrackWithHeavyEquipment.textContent = 'Menyinkronkan...';

                    fetch('/api/sync-cartrack-with-heavy-equipment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            console.log(data);
                            syncCartrack.disabled = false;
                            syncCartrackWithHeavyEquipment.disabled = false;
                            syncCartrackWithHeavyEquipment.textContent =
                                'Sinkronisasi Data dengan Heavy Equipment';
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menyinkronkan data dengan Heavy Equipment.');
                            syncCartrack.disabled = false;
                            syncCartrackWithHeavyEquipment.disabled = false;
                            syncCartrackWithHeavyEquipment.textContent =
                                'Sinkronisasi Data dengan Heavy Equipment';
                        });

                });
            });
        </script>
    @endpush
</x-app-layout>

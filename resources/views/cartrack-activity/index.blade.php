<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cartrack Activity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">

                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Sinkronisasi Terakhir</h3>
                            <p>{{ Carbon\Carbon::parse($last_sync)->format('d-M-Y') }}
                                ({{ Carbon\Carbon::parse($last_sync)->diffForHumans() }})</p>
                            <input type="hidden" name="last_sync_raw" id="last_sync_raw"
                                value="{{ Carbon\Carbon::parse($last_sync)->format('Y-m-d') }}">
                        </div>
                        <div class="flex items-center space-x-4 justify-center">
                            <x-text-input id="sync_date" placeholder="Periode" class="mt-2" />
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" style="width:270px">
                                <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Sinkronisasi Data
                            </button>
                        </div>
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
                                        Trip ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kendaraan Berat</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cartrack Vehicle</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lokasi Awal</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lokasi Akhir</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Durasi Trip</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($cartrack_activities as $key => $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $key + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->trip_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('alat-berat.edit', ['alat_berat' => $item->cartrack_vehicle->heavyEquipment()->first() ? $item->cartrack_vehicle->heavyEquipment()->first()->id : '#']) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                                                {{ $item->cartrack_vehicle->heavyEquipment()->first() ? $item->cartrack_vehicle->heavyEquipment()->first()->name : 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->cartrack_vehicle ? $item->cartrack_vehicle->manufacturer . ' ' . $item->cartrack_vehicle->model : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->start_location }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->end_location ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $item->trip_duration ?? '-' }}</td>
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
                        {{ $cartrack_activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const lastSyncInput = document.getElementById('last_sync_raw').value;

                flatpickr("#sync_date", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    defaultDate: [
                        lastSyncInput,
                        new Date(lastSyncInput).setDate(new Date(lastSyncInput).getDate() + 7)
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
                        }
                    }
                });

                const syncButton = document.querySelector('button[type="button"]');
                syncButton.addEventListener('click', function() {
                    const sync_date = document.getElementById('sync_date').value;
                    syncButton.disabled = true;
                    syncButton.textContent = 'Menyinkronkan...';

                    fetch('/api/sync-cartrack-activity', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            },
                            body: JSON.stringify({
                                // last_sync: lastSyncInput
                                start_timestamp: sync_date.split(' to ')[0],
                                end_timestamp: sync_date.split(' to ')[1] || sync_date.split(
                                    ' to ')[0]
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            console.log(data);
                            syncButton.disabled = false;
                            syncButton.textContent = 'Sinkronisasi Data';
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menyinkronkan data.');
                            syncButton.disabled = false;
                            syncButton.textContent = 'Sinkronisasi Data';
                        });

                });
            });
        </script>
    @endpush
</x-app-layout>

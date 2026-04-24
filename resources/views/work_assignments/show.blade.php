@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endpush
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pekerjaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Informasi Pekerjaan</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Nama Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->project_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Alat Berat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->heavyEquipment->name }} ({{ $workAssignment->heavyEquipment->nomor_lambung }})</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->city->name }}, {{ $workAssignment->district->name }}, {{ $workAssignment->village->name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->start_date->format('d/m/Y') }} - {{ $workAssignment->end_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Tipe Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->tipe_pekerjaan }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Permasalahan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->permasalahan ?? 'Tidak ada' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Panjang Penanganan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->panjang_penanganan ?? 'Tidak ada' }} km</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $workAssignment->alamat ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Link Dokumentasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($workAssignment->documentation_link)
                                    <a href="{{ $workAssignment->documentation_link }}" target="_blank" class="text-blue-600 hover:underline">
                                        Lihat Dokumentasi
                                    </a>
                                @else
                                    Tidak ada
                                @endif
                            </dd>
                        </div>

                    </dl>
                    <h3 class="text-lg font-semibold mt-8 mb-4">Lokasi</h3>
                    <div id="map" class="mt-1 text-sm z-10 text-gray-900" style="height: 200px;"></div>

                    <h3 class="text-lg font-semibold mt-8 mb-4">Foto Lainnya</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @forelse($workAssignment->fieldConditionPhotos as $photo)
                            @if($photo->uploader->isAdmin())
                                <div class="relative">
                                    <a href="{{ asset($photo->photo_path) }}" data-lightbox="admin-field-condition" data-title="Kondisi Lapangan Lainnya">
                                        <img src="{{ asset($photo->photo_path) }}" alt="Kondisi Lapangan" class="w-full h-40 object-cover rounded-lg">
                                    </a>
                                    <button onclick="showDeleteModal('{{ route('delete.image', ['type' => 'field_condition', 'id' => $photo->id]) }}')" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-2 left-2 text-white text-xs bg-black bg-opacity-50 px-2 py-1 rounded">
                                        {{ $photo->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-span-full">
                                <p class="text-gray-500 text-sm">Belum ada foto kondisi lapangan lainnya.</p>
                            </div>
                        @endforelse
                    </div>

                    <h3 class="text-lg font-semibold mt-8 mb-4">Riwayat Operator</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($operators as $operator)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $operator->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $operator->start_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $operator->end_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($operator->deleted_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Diganti</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-semibold mt-8 mb-4">Riwayat Helper</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($helpers as $helper)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $helper->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $helper->start_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $helper->end_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($helper->deleted_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Diganti</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-semibold mt-8 mb-4">Riwayat Absensi dan Aktivitas</h3>
                    @forelse($attendanceLogs as $date => $logs)
                        <div class="mb-8 p-4 border rounded-lg">
                            <h4 class="text-md font-semibold mb-4">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h4>
                            @foreach($logs as $log)
                                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900 mb-2">
                                        {{ $log->log_type === 'attendance' ? 'Absensi Harian' : 'Check-in Pekerjaan' }}
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h5 class="text-sm font-semibold mb-2">
                                                {{ $log->log_type === 'attendance' ? 'Absen Masuk' : 'Check-in' }}
                                                ({{ $log->check_in_time->format('H:i') }})
                                            </h5>
                                            <div class="flex flex-col space-y-2">
                                                @if($log->check_in_photo)
                                                    <div class="relative">
                                                        <a href="{{ asset($log->check_in_photo) }}"
                                                        data-lightbox="check-in-{{ $log->id }}"
                                                        data-title="{{ $log->log_type === 'attendance' ? 'Foto Absen Masuk' : 'Foto Check-in' }} {{ $log->check_in_time->format('d F Y H:i') }}">
                                                            <img src="{{ asset($log->check_in_photo) }}"
                                                                alt="{{ $log->log_type === 'attendance' ? 'Foto Absen Masuk' : 'Foto Check-in' }}"
                                                                class="w-full h-40 object-cover rounded-lg">
                                                        </a>
                                                        <button onclick="showDeleteModal('{{ route('delete.image', ['type' => 'check_in', 'id' => $log->id]) }}')"
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif

                                                @if($log->log_type === 'work')
                                                    <div class="flex items-center space-x-2">
                                                        <p>Hours Meter Start: {{ $log->hours_meter_start ?? 'Tidak tersedia' }}</p>
                                                        <button
                                                            onclick="showHoursMeterEditModal('start', {{ $log->id }})"
                                                            class="text-blue-500 hover:text-blue-700 focus:outline-none"
                                                            title="Edit Hours Meter Start"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-semibold mb-2">
                                                {{ $log->log_type === 'attendance' ? 'Absen Keluar' : 'Check-out' }}
                                                ({{ $log->check_out_time ? $log->check_out_time->format('H:i') : 'Belum selesai' }})
                                            </h5>
                                            <div class="flex flex-col space-y-2">
                                                @if($log->check_out_photo)
                                                    <div class="relative">
                                                        <a href="{{ asset($log->check_out_photo) }}"
                                                        data-lightbox="check-out-{{ $log->id }}"
                                                        data-title="{{ $log->log_type === 'attendance' ? 'Foto Absen Keluar' : 'Foto Check-out' }} {{ $log->check_out_time ? $log->check_out_time->format('d F Y H:i') : 'Belum selesai' }}">
                                                            <img src="{{ asset($log->check_out_photo) }}"
                                                                alt="{{ $log->log_type === 'attendance' ? 'Foto Absen Keluar' : 'Foto Check-out' }}"
                                                                class="w-full h-40 object-cover rounded-lg">
                                                        </a>
                                                        <button onclick="showDeleteModal('{{ route('delete.image', ['type' => 'check_out', 'id' => $log->id]) }}')"
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif

                                                @if($log->log_type === 'work')
                                                    <div class="flex items-center space-x-2">
                                                        <p>Hours Meter End: {{ $log->hours_meter_end ?? 'Tidak tersedia' }}</p>
                                                        <button
                                                            onclick="showHoursMeterEditModal('end', {{ $log->id }})"
                                                            class="text-blue-500 hover:text-blue-700 focus:outline-none"
                                                            title="Edit Hours Meter End"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(isset($fieldConditionPhotos[$date]))
                                <h5 class="text-sm font-semibold mt-4 mb-2">Foto Kondisi Lapangan:</h5>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach($fieldConditionPhotos[$date] as $photo)
                                        @if(!$photo->uploader->isAdmin())
                                            <div class="relative">
                                                <a href="{{ asset($photo->photo_path) }}" data-lightbox="field-condition-{{ $date }}" data-title="Kondisi Lapangan {{ \Carbon\Carbon::parse($date)->format('d F Y') }}">
                                                    <img src="{{ asset($photo->photo_path) }}" alt="Kondisi Lapangan" class="w-full h-40 object-cover rounded-lg">
                                                </a>
                                                <button onclick="showDeleteModal('{{ route('delete.image', ['type' => 'field_condition', 'id' => $photo->id]) }}')" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">Tidak ada data absensi yang tersedia.</p>
                    @endforelse

                    <div class="mt-8">
                        <a href="{{ route('work-assignments.index') }}"
                            class=" bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Kembali ke Daftar Pekerjaan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit Hours Meter -->
    <div x-data="{ showHoursMeterEditModal: false, type: '', logId: null }"
        x-show="showHoursMeterEditModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        @hours-meter-modal.window="showHoursMeterEditModal = true; type = $event.detail.type; logId = $event.detail.logId">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="hoursMeterForm" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="'Edit Hours Meter ' + (type === 'start' ? 'Start' : 'End')"></h3>
                                <div class="mt-4">
                                    <label for="hours_meter" class="block text-sm font-medium text-gray-700">Hours Meter Value</label>
                                    <input type="number"
                                            name="hours_meter"
                                            id="hours_meter"
                                            step="0.1"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan
                        </button>
                        <button type="button" @click="showHoursMeterEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Hapus Gambar
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus gambar ini? Tindakan ini tidak dapat dibatalkan.
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
    @push('styles')
        <style>
            .top-2{
                top: 6px;
            }
            .right-2{
                right: 2px;
            }
        </style>

    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if ($workAssignment->latitude && $workAssignment->longitude)
                    var map = L.map('map', {
                        center: [{{ $workAssignment->latitude }}, {{ $workAssignment->longitude }}],
                        zoom: 20,
                        dragging: true,
                        touchZoom: true,
                        scrollWheelZoom: true,
                        doubleClickZoom: false,
                        boxZoom: false,
                        tap: false,
                        keyboard: false,
                        zoomControl: false
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; PU Cimancis'
                    }).addTo(map);

                    L.marker([{{ $workAssignment->latitude }}, {{ $workAssignment->longitude }}]).addTo(map);
                @endif

                // Initialize Lightbox
                lightbox.option({
                    'resizeDuration': 200,
                    'wrapAround': true
                });
            });
            let deleteUrl = '';

            function showDeleteModal(url) {
                deleteUrl = url;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function hideDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            function deleteImage() {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal menghapus gambar: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus gambar.');
                })
                .finally(() => {
                    hideDeleteModal();
                });
            }

            document.getElementById('confirmDelete').addEventListener('click', deleteImage);
            document.getElementById('cancelDelete').addEventListener('click', hideDeleteModal);
        </script>
        <script>
            function showHoursMeterEditModal(type, logId) {
                const event = new CustomEvent('hours-meter-modal', {
                    detail: { type, logId }
                });
                window.dispatchEvent(event);

                // Set form action URL
                const form = document.getElementById('hoursMeterForm');
                form.action = `/admin/attendance-logs/${logId}/update-hours-meter/${type}`;
            }

            document.getElementById('hoursMeterForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                try {
                    const response = await fetch(this.action, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            hours_meter: document.getElementById('hours_meter').value
                        })
                    });

                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Terjadi kesalahan saat menyimpan data');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data');
                }
            });
        </script>
    @endpush
</x-app-layout>

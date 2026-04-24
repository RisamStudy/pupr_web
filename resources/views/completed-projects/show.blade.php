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
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->project_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Alat Berat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->heavyEquipment->name }} ({{ $completedProject->heavyEquipment->nomor_lambung }})</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Lokasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->city->name }}, {{ $completedProject->district->name }}, {{ $completedProject->village->name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->start_date->format('d/m/Y') }} - {{ $completedProject->end_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Tipe Pekerjaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->tipe_pekerjaan }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Permasalahan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->permasalahan ?? 'Tidak ada' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Panjang Penanganan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->panjang_penanganan ?? 'Tidak ada' }} km</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $completedProject->alamat ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Link Dokumentasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($completedProject->documentation_link)
                                    <a href="{{ $completedProject->documentation_link }}" target="_blank" class="text-blue-600 hover:underline">
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
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Diubah</span>
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
            <!-- Check-in Section -->
            <div>
                <h5 class="text-sm font-semibold mb-2">
                    {{ $log->log_type === 'attendance' ? 'Absen Masuk' : 'Check-in' }} 
                    ({{ $log->check_in_time->format('H:i') }})
                </h5>
                <div class="flex flex-col space-y-2">
                    @if($log->check_in_photo)
                        <div class="relative group">
                            <a href="{{ asset($log->check_in_photo) }}" 
                               data-lightbox="check-in-{{ $log->id }}" 
                               data-title="{{ $log->log_type === 'attendance' ? 'Foto Absen Masuk' : 'Foto Check-in' }} {{ $log->check_in_time->format('d F Y H:i') }}">
                                <img src="{{ asset($log->check_in_photo) }}" 
                                     alt="{{ $log->log_type === 'attendance' ? 'Foto Absen Masuk' : 'Foto Check-in' }}" 
                                     class="w-full h-40 object-cover rounded-lg">
                            </a>
                        </div>
                    @endif
                    
                    @if($log->log_type === 'work')
                        <p class="text-sm text-gray-600">Hours Meter Start: {{ $log->hours_meter_start ?? 'Tidak tersedia' }}</p>
                        @if($log->hours_meter_start_photo)
                            <div class="relative group">
                                <a href="{{ asset($log->hours_meter_start_photo) }}" 
                                   data-lightbox="hours-start-{{ $log->id }}" 
                                   data-title="Foto Hours Meter Awal {{ $log->check_in_time->format('d F Y H:i') }}">
                                    <img src="{{ asset($log->hours_meter_start_photo) }}" 
                                         alt="Foto Hours Meter Awal" 
                                         class="w-full h-40 object-cover rounded-lg">
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Check-out Section -->
            <div>
                <h5 class="text-sm font-semibold mb-2">
                    {{ $log->log_type === 'attendance' ? 'Absen Keluar' : 'Check-out' }}
                    ({{ $log->check_out_time ? $log->check_out_time->format('H:i') : 'Belum selesai' }})
                </h5>
                <div class="flex flex-col space-y-2">
                    @if($log->check_out_photo)
                        <div class="relative group">
                            <a href="{{ asset($log->check_out_photo) }}" 
                               data-lightbox="check-out-{{ $log->id }}" 
                               data-title="{{ $log->log_type === 'attendance' ? 'Foto Absen Keluar' : 'Foto Check-out' }} {{ $log->check_out_time ? $log->check_out_time->format('d F Y H:i') : '' }}">
                                <img src="{{ asset($log->check_out_photo) }}" 
                                     alt="{{ $log->log_type === 'attendance' ? 'Foto Absen Keluar' : 'Foto Check-out' }}" 
                                     class="w-full h-40 object-cover rounded-lg">
                            </a>
                        </div>
                    @endif

                    @if($log->log_type === 'work')
                        <p class="text-sm text-gray-600">Hours Meter End: {{ $log->hours_meter_end ?? 'Tidak tersedia' }}</p>
                        @if($log->hours_meter_end_photo)
                            <div class="relative group">
                                <a href="{{ asset($log->hours_meter_end_photo) }}" 
                                   data-lightbox="hours-end-{{ $log->id }}" 
                                   data-title="Foto Hours Meter Akhir {{ $log->check_out_time ? $log->check_out_time->format('d F Y H:i') : '' }}">
                                    <img src="{{ asset($log->hours_meter_end_photo) }}" 
                                         alt="Foto Hours Meter Akhir" 
                                         class="w-full h-40 object-cover rounded-lg">
                                </a>
                            </div>
                        @endif
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
                                        <a href="{{ asset($photo->photo_path) }}" data-lightbox="field-condition-{{ $date }}" data-title="Kondisi Lapangan {{ \Carbon\Carbon::parse($date)->format('d F Y') }}">
                                            <img src="{{ asset($photo->photo_path) }}" alt="Kondisi Lapangan" class="w-full h-40 object-cover rounded-lg">
                                        </a>
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
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if ($completedProject->latitude && $completedProject->longitude)
                    var map = L.map('map', {
                        center: [{{ $completedProject->latitude }}, {{ $completedProject->longitude }}],
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

                    L.marker([{{ $completedProject->latitude }}, {{ $completedProject->longitude }}]).addTo(map);
                @endif

                // Initialize Lightbox
                lightbox.option({
                    'resizeDuration': 200,
                    'wrapAround': true
                });
            });
        </script>
    @endpush
</x-app-layout>

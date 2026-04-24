<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Alat Berat') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, equipmentToDelete: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('alat-berat.index') }}" method="GET" class="mb-4">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="flex items-center mb-4 md:mb-0 md:mr-4">
                        <label for="status" class="mr-2">Status:</label>
                        <select name="status" id="status" class="w-full border bg-white border-gray-300 rounded px-2 py-1">
                            <option value="">All</option>
                            <option value="beroperasi" {{ request('status') == 'beroperasi' ? 'selected' : '' }}>Beroperasi</option>
                            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="tidak ada" {{ request('status') == 'tidak ada' ? 'selected' : '' }}>Tidak Ada</option>
                        </select>
                    </div>
                    <div class="flex items-center mb-4 md:mb-0 md:mr-4">
                        <label for="kondisi" class="mr-2">Kondisi:</label>
                        <select name="kondisi" id="kondisi" class="w-full border bg-white border-gray-300 rounded px-2 py-1">
                            <option value="">All</option>
                            <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                </div>
            </form>
            <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a href="{{ route('alat-berat.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Tambah Data Baru</a>

                    <table class="min-w-full divide-y divide-gray-200 mt-4">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Lambung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Alat Berat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Meter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if ($heavyEquipments->isEmpty())
                                <tr>
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center">
                                        Tidak ada data alat berat yang tersedia.
                                    </td>
                                </tr>
                            @else
                                @foreach ($heavyEquipments as $key => $equipment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $key+1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $equipment->nomor_lambung }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('alat-berat.show', $equipment->id) }}"
                                           class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                                            {{ $equipment->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $equipment->tahun }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $equipment->merek }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" x-data="{ open: false, kondisi: '{{ ucwords(str_replace('_', ' ', $equipment->kondisi)) }}' }">
                                        <div class="relative">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <span x-text="kondisi" :class="{
                                                    'text-yellow-600': kondisi === 'Baik',
                                                    'text-green-600': kondisi === 'Rusak Ringan',
                                                    'text-blue-600': kondisi === 'Rusak Berat'
                                                }"></span>
                                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5  z-50">
                                                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                                    <form id="updateStatusForm{{ $equipment->id }}" action="{{ route('alat-berat.updateKondisi', $equipment) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" @click="kondisi = 'Baik'; open = false" name="kondisi" value="baik" class="block w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100 hover:text-yellow-700" role="menuitem">Baik</button>
                                                        <button type="submit" @click="kondisi = 'Rusak Ringan'; open = false" name="kondisi" value="rusak_ringan" class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 hover:text-green-700" role="menuitem">Rusak Ringan</button>
                                                        <button type="submit" @click="kondisi = 'Rusak Berat'; open = false" name="kondisi" value="rusak_berat" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-gray-100 hover:text-blue-700" role="menuitem">Rusak Berat</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                    @php
                                        $statusClasses = [
                                            'ready' => 'bg-green-200 text-green-800',
                                            'beroperasi' => 'bg-blue-200 text-blue-800',
                                            'maintenance' => 'bg-orange-200 text-orange-800',
                                            'rusak' => 'bg-red-200 text-red-800',
                                            'tidak ada' => 'bg-gray-300 text-gray-800',
                                        ];

                                        $badgeClass = $statusClasses[$equipment->status] ?? 'bg-gray-200 text-gray-700';
                                    @endphp

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-sm font-semibold rounded {{ $badgeClass }}">
                                            {{ ucwords(str_replace('_', ' ', $equipment->status)) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $equipment->location }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $equipment->hours_meter ?? '-'}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('alat-berat.show', $equipment) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat</a>
                                        <a href="{{ route('alat-berat.edit', $equipment) }}" class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                        <button @click="showDeleteModal = true; equipmentToDelete = {{ $equipment->id }}" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>

            </div>
            <div class="mt-4">
                @if ($heavyEquipments->hasPages())
                    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
                        <div class="flex justify-between flex-1 sm:hidden">
                            @if ($heavyEquipments->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    {!! __('pagination.previous') !!}
                                </span>
                            @else
                                <a href="{{ $heavyEquipments->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    {!! __('pagination.previous') !!}
                                </a>
                            @endif

                            @if ($heavyEquipments->hasMorePages())
                                <a href="{{ $heavyEquipments->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    {!! __('pagination.next') !!}
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    {!! __('pagination.next') !!}
                                </span>
                            @endif
                        </div>

                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            {{ $heavyEquipments->links() }}
                        </div>
                    </nav>
                @endif
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <x-delete-modal show="showDeleteModal">
            <x-slot name="content">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Delete Data
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this data? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <form :action="'{{ route('alat-berat.destroy', '') }}/' + equipmentToDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button @click="showDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </x-slot>
        </x-delete-modal>
    </div>
</x-app-layout>

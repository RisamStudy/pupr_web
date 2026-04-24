<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Pekerjaan') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, assignmentToDelete: null, showPhotoModal: false, currentAssignment: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex flex-col md:flex-row md:justify-between md:items-center">
                        <a href="{{ route('work-assignments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2 md:mb-0">
                            Tambah Pekerjaan
                        </a>
                        <form action="{{ route('work-assignments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <!-- Year Filter -->
                            <div class="w-full md:w-40">
                                <select id="year" name="year" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            Tahun {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="w-full md:w-48">
                                <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Dimulai" {{ request('status') == 'Belum Dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                                    <option value="Sedang Berlangsung" {{ request('status') == 'Sedang Berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>

                            <!-- Search Box -->
                            <div class="flex w-full md:w-auto flex-grow">
                                <input type="text"
                                       name="search"
                                       placeholder="Cari..."
                                       value="{{ request('search') }}"
                                       class="rounded-l-md border-t border-b border-l border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                <button type="submit" class="px-4 rounded-r-md bg-blue-500 text-white font-bold hover:bg-blue-700">
                                    Cari
                                </button>
                            </div>
                            @if(request()->has('page'))
                                <input type="hidden" name="page" value="{{ request('page') }}">
                            @endif
                            @if(request()->has('year'))
                                <input type="hidden" name="year" value="{{ request('year') }}">
                            @endif
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pekerjaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Lambung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat yang Digunakan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Helper</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Pekerjaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($workAssignments as $key => $assignment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ ($workAssignments->currentPage() - 1) * $workAssignments->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->project_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->heavyEquipment->nomor_lambung }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->heavyEquipment->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($assignment->assignmentUsers->where('role', 'operator') as $operator)
                                                <div>{{ $operator->user->name }}</div>
                                            @endforeach
                                            <a href="{{ route('work-assignments.manage-users', ['assignment' => $assignment->id, 'role' => 'operator']) }}" class="text-blue-600 hover:text-blue-900">Kelola Operator</a>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($assignment->assignmentUsers->where('role', 'helper') as $helper)
                                                <div>{{ $helper->user->name }}</div>
                                            @endforeach
                                            <a href="{{ route('work-assignments.manage-users', ['assignment' => $assignment->id, 'role' => 'helper']) }}" class="text-blue-600 hover:text-blue-900">Kelola Helper</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->city->name }}, {{ $assignment->district->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->tipe_pekerjaan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->start_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->end_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 relative " x-data="{ open: false, status: '{{ $assignment->status }}' }">
                                            <div class="relative">
                                                <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <span x-text="status" :class="{
                                                        'text-yellow-600': status === 'Belum Dimulai',
                                                        'text-green-600': status === 'Sedang Berlangsung',
                                                        'text-blue-600': status === 'Selesai'
                                                    }"></span>
                                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5  z-50">
                                                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                                        <form id="updateStatusForm{{ $assignment->id }}" action="{{ route('work-assignments.updateStatus', $assignment) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" @click="status = 'Belum Dimulai'; open = false" name="status" value="Belum Dimulai" class="block w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100 hover:text-yellow-700" role="menuitem">Belum Dimulai</button>
                                                            <button type="submit" @click="status = 'Sedang Berlangsung'; open = false" name="status" value="Sedang Berlangsung" class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 hover:text-green-700" role="menuitem">Sedang Berlangsung</button>
                                                            <button type="submit" @click="status = 'Selesai'; open = false" name="status" value="Selesai" class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-gray-100 hover:text-blue-700" role="menuitem">Selesai</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button
                                                    @click="showPhotoModal = true; currentAssignment = {{ $assignment->id }}"
                                                    class="text-blue-600 hover:text-blue-900 inline-flex items-center"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                                                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
                                                    </svg>
                                                    <span>Tambah Foto</span>
                                                </button>
                                                <a href="{{ route('work-assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                                <a href="{{ route('work-assignments.edit', ['work_assignment' => $assignment->id, 'page' => $workAssignments->currentPage(), 'year' => request('year')]) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                                <button @click="showDeleteModal = true; assignmentToDelete = {{ $assignment->id }}" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        @if ($workAssignments->hasPages())
                            <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
                                <div class="flex justify-between flex-1 sm:hidden">
                                    @if ($workAssignments->onFirstPage())
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                            {!! __('pagination.previous') !!}
                                        </span>
                                    @else
                                        <a href="{{ $workAssignments->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                            {!! __('pagination.previous') !!}
                                        </a>
                                    @endif

                                    @if ($workAssignments->hasMorePages())
                                        <a href="{{ $workAssignments->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                            {!! __('pagination.next') !!}
                                        </a>
                                    @else
                                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                            {!! __('pagination.next') !!}
                                        </span>
                                    @endif
                                </div>

                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    {{ $workAssignments->links() }}
                                </div>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Photo Upload Modal -->
        <div x-show="showPhotoModal"
                x-data="{
                    filesPreview: [],
                    filesCount: 0,
                    handleFiles(e) {
                        const files = e.target.files;
                        this.filesCount = files.length;
                        this.filesPreview = [];
                        for (let i = 0; i < Math.min(files.length, 4); i++) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.filesPreview.push(e.target.result);
                            };
                            reader.readAsDataURL(files[i]);
                        }
                    }
                }"
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        aria-hidden="true"
                        @click="showPhotoModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="`/admin/work-assignments/${currentAssignment}/photos`"
                            method="POST"
                            enctype="multipart/form-data"
                            class="w-full">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Upload Foto Kondisi Lapangan
                                </h3>
                                <button type="button" @click="showPhotoModal = false" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-4">
                                <div class="flex flex-col items-center space-y-4">
                                    <label class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue-500 rounded-lg shadow-lg tracking-wide uppercase border-2 border-blue-500 border-dashed cursor-pointer hover:bg-blue-500 hover:text-white transition-colors duration-300">
                                        <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                                        </svg>
                                        <span class="mt-2 text-base" x-text="filesCount ? `${filesCount} file(s) selected` : 'Select Files'"></span>
                                        <input type="file"
                                                class="hidden"
                                                name="photos[]"
                                                multiple
                                                accept="image/*"
                                                required
                                                @change="handleFiles($event)" />
                                    </label>

                                    <!-- Preview Section -->
                                    <div x-show="filesPreview.length"
                                            class="grid grid-cols-2 gap-4 w-full">
                                        <template x-for="(preview, index) in filesPreview" :key="index">
                                            <div class="relative aspect-w-16 aspect-h-9">
                                                <img :src="preview"
                                                        class="rounded-lg object-cover w-full h-full"
                                                        alt="Preview" />
                                            </div>
                                        </template>
                                    </div>

                                    <p class="text-xs text-gray-500 text-center">
                                        Format yang didukung: JPG, PNG, GIF.<br>
                                        <span x-show="filesCount > 4" class="text-blue-500">
                                            +<span x-text="filesCount - 4"></span> more files selected
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 gap-3 px-4 py-3 sm:px-6 flex flex-row-reverse">
                            <button type="submit"
                                    class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Upload Foto
                            </button>
                            <button type="button"
                                    @click="showPhotoModal = false"
                                    class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
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
                <form :action="'{{ route('work-assignments.destroy', '') }}/' + assignmentToDelete" method="POST">
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
    @push('styles')
        <style>
            .aspect-w-16 {
                position: relative;
                padding-bottom: 56.25%;
            }

            .aspect-w-16 > * {
                position: absolute;
                height: 100%;
                width: 100%;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }
        </style>
    @endpush
    @push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form when year or status changes
            const yearSelect = document.getElementById('year');
            const statusSelect = document.getElementById('status');

            yearSelect.addEventListener('change', function() {
                this.form.submit();
            });

            statusSelect.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
    @endpush
</x-app-layout>

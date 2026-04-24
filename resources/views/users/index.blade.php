<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Pegawai') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, userToDelete: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Fixed Header Section -->
                <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Filter Section -->
                        <form action="{{ route('users.index') }}" method="GET" class="mb-4 md:mb-0">
                            <div class="flex items-center">
                                <label for="role" class="mr-2 whitespace-nowrap">Role:</label>
                                <select name="role" id="role" class="w-48 border bg-white border-gray-300 rounded px-2 py-1">
                                    <option value="">All</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                                    <option value="helper" {{ request('role') == 'helper' ? 'selected' : '' }}>Helper</option>
                                </select>
                                <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <!-- Add User Button -->
                        <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded inline-block">
                            Tambah Pengguna Baru
                        </a>
                    </div>
                </div>

                <!-- Table Section with Fixed Height -->
                <div class="overflow-x-auto">
                    <div class="overflow-y-auto max-h-[calc(100vh-180px)]">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="group px-6 py-3 text-left">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">No</span>
                                        </div>
                                    </th>
                                    <th class="group px-6 py-3 text-left">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</span>
                                        </div>
                                    </th>
                                    <th class="group px-6 py-3 text-left">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</span>
                                        </div>
                                    </th>
                                    <th class="group px-6 py-3 text-left">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Role</span>
                                        </div>
                                    </th>
                                    <th class="group px-6 py-3 text-left">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</span>
                                        </div>
                                    </th>
                                    <th class="group px-6 py-3 text-left">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if ($users->isEmpty())
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            Tidak ada data pegawai yang tersedia.
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($users as $key => $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $key+1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if (!empty($user->roles) && is_array($user->roles))
                                                @foreach ($user->roles as $role)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                        {{ ucfirst($role) }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-500">Tidak ada role</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->status == 'tersedia')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            @elseif ($user->status == 'bertugas')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            @elseif($user->status == 'tidak ada')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ ucwords(str_replace('_',' ',$user->status)) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Admin
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat</a>
                                            <a href="{{ route('users.edit', $user) }}" class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                            <button @click="showDeleteModal = true; userToDelete = {{ $user->id }}" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
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
                            Delete User
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this user? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <form :action="'{{ route('users.destroy', '') }}/' + userToDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
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
            .table-wrapper{
                max-height: 600px;
            }
            .overflow-y-auto::-webkit-scrollbar {
                width: 8px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            /* Ensure the sticky header has a background and shadow */
            thead.sticky {
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            /* Ensure the fixed header section stays on top */
            .sticky.top-0 {
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
        </style>

    @endpush
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <div class="mb-6 flex items-center">
                        <div class="mr-6">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-2xl font-medium text-gray-900">{{ $user->name }}</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Current Work Location -->
                    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg shadow-sm">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Lokasi Pekerjaan Saat Ini</h4>
                        @if($currentAssignment)
                            <p class="text-sm text-gray-600">{{ $currentAssignment->project_name }}</p>
                            <p class="text-sm text-gray-600">{{ $currentAssignment->alamat }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $currentAssignment->village->name ?? '' }},
                                {{ $currentAssignment->district->name ?? '' }},
                                {{ $currentAssignment->city->name ?? '' }}
                            </p>
                        @else
                            <p class="text-sm text-gray-600">Tidak ada penugasan aktif saat ini.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Roles</p>
                            <div class="mt-1">
                                @if(is_array($user->roles) && !empty($user->roles))
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-900">Tidak ada role</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Types</p>
                            <div class="mt-1">
                                @if(is_array($user->types) && !empty($user->types))
                                    @foreach($user->types as $type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                            {{ ucwords(str_replace('_', ' ', $type)) }}
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-900">Tidak ada tipe</p>
                                @endif
                            </div>
                        </div>

                        @if(!is_array($user->roles) || !in_array('admin', $user->roles))
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="mt-1 text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $user->status)) }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Work History Section -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Riwayat Pekerjaan</h4>
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                            @php
                                $allAssignments = $user->workAssignments()->with(['assignmentUsers' => function($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                }, 'village', 'district', 'city'])->get();
                            @endphp
                            @if($allAssignments->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Panjang Penanganan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($allAssignments as $assignment)
                                                @php
                                                    $userAssignment = $assignment->assignmentUsers->first();
                                                @endphp
                                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        <a href="{{ route('work-assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            {{ $assignment->project_name }}
                                                        </a>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ ucfirst($userAssignment->role) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $userAssignment->start_date ? $userAssignment->start_date : 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $userAssignment->end_date ? $userAssignment->end_date : 'Ongoing' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $assignment->panjang_penanganan ?? 'N/A' }} m
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-700 p-4">Tidak ada riwayat pekerjaan.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Work Hours Section -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Jam Kerja</h4>
                        <form action="{{ route('users.show', $user) }}" method="GET" class="mb-4 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-wrap items-end gap-4">
                                <div>
                                    <label for="filter_type" class="block text-sm font-medium text-gray-700 mb-1">Filter</label>
                                    <select name="filter_type" id="filter_type" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="all" {{ $filterType == 'all' ? 'selected' : '' }}>Semua</option>
                                        <option value="week" {{ $filterType == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                                        <option value="month" {{ $filterType == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                        <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                                <div id="custom-date-range" class="flex gap-2" style="{{ $filterType == 'custom' ? '' : 'display: none;' }}">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                        <input type="date" id="start_date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                        <input type="date" id="end_date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <p class="text-lg font-semibold text-gray-800">Total Jam Kerja: <span class="text-indigo-600">{{ $workHours['total_hours'] }} jam</span></p>
                            <p class="text-lg font-semibold text-gray-800 mt-2">Total Hours Meter: <span class="text-indigo-600">{{ $workHours['total_hours_meter'] }} jam</span></p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-4">
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Edit
                        </a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelect = document.getElementById('filter_type');
            const customDateRange = document.getElementById('custom-date-range');

            filterSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.style.display = 'flex';
                } else {
                    customDateRange.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

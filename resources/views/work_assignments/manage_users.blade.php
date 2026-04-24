<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola ' . ucfirst($role) . ' untuk ' . $assignment->project_name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Durasi Pekerjaan</h3>
                    <p class="mb-2">{{ $assignment->start_date->format('d/m/Y')}} - {{ $assignment->end_date->format('d/m/Y')}} ({{ $assignment->expected_duration }} Hari)</p>
                    <h3 class="text-lg font-semibold mb-4">{{ ucfirst($role) }} Saat Ini</h3>
                    @if($assignment->assignmentUsers->where('role', $role)->count() > 0)
                        <ul class="mb-6">
                            @foreach($assignment->assignmentUsers->where('role', $role) as $assignmentUser)
                                <li class="mb-2 flex items-center justify-between">
                                    <span>
                                        {{ $assignmentUser->user->name }}
                                        ({{ $assignmentUser->start_date }} - {{ $assignmentUser->end_date }})
                                    </span>
                                    <form action="{{ route('work-assignments.remove-user', $assignmentUser) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Hapus</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-6 text-gray-500">Belum ada {{ strtolower($role) }} yang ditugaskan.</p>
                    @endif

                    <h3 class="text-lg font-semibold mt-6 mb-4">Tambah {{ ucfirst($role) }} Baru</h3>
                    <form action="{{ route('work-assignments.add-user', $assignment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="{{ $role }}">
                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Pilih {{ ucfirst($role) }}</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Pilih {{ ucfirst($role) }}</option>
                                @foreach($availableUsers as $user)
                                    @if (!$user->hasRole('admin') && $user->status === 'tersedia')
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>
                        <div class="mb-4">
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Tambah {{ ucfirst($role) }}
                            </button>
                            <a href="{{ route('work-assignments.index') }}" class="text-gray-600 hover:text-gray-900">Kembali ke Daftar Pekerjaan</a>
                        </div>
                    </form>
                    <!-- Tambahkan section riwayat -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Riwayat {{ ucfirst($role) }}</h3>
                        <div class="bg-gray-100 rounded-lg p-4">
                            @php
                                $projectStartDate = $assignment->start_date;
                                $historicalAssignments = $assignment->assignmentUsers()
                                    ->where('role', $role)
                                    ->onlyTrashed()
                                    ->with('user')
                                    ->orderBy('deleted_at', 'desc')
                                    ->get()
                                    ->groupBy(function($assignment) use ($projectStartDate) {
                                        // Ubah tanggal ke Carbon jika belum
                                        $deleteDate = Carbon\Carbon::parse($assignment->deleted_at);

                                        // Hitung minggu ke berapa dari tanggal mulai proyek ke tanggal penghapusan
                                        $weeksBetween = $projectStartDate->diffInWeeks($deleteDate, false);

                                        // Jika hasilnya negatif (tanggal penghapusan sebelum mulai proyek),
                                        // kita ubah jadi minggu ke-0
                                        return max(1, $weeksBetween + 1);
                                    });
                            @endphp

                            @if($historicalAssignments->count() > 0)
                                <div class="space-y-6">
                                    @foreach($historicalAssignments->sortKeys() as $weekNumber => $assignments)
                                        @php
                                            // Hitung tanggal awal dan akhir minggu
                                            $startOfWeek = $projectStartDate->copy()->addWeeks($weekNumber - 1);
                                            $endOfWeek = $startOfWeek->copy()->addDays(6);

                                        @endphp
                                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                            <div class="bg-gray-50 px-4 py-2 border-b">
                                                <h4 class="font-medium text-gray-700 text-lg">
                                                    Minggu ke-{{ $weekNumber }}
                                                    ({{ $startOfWeek->format('d/m/Y') }} - {{ $endOfWeek->format('d/m/Y') }})
                                                </h4>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Diganti</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach($assignments->sortBy('deleted_at') as $historicalAssignment)
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    {{ $historicalAssignment->user->name }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    {{ Carbon\Carbon::parse($historicalAssignment->start_date)->format('d/m/Y') }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    {{ Carbon\Carbon::parse($historicalAssignment->end_date)->format('d/m/Y') }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                                                    {{ $historicalAssignment->deleted_at->format('d/m/Y H:i') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Tidak ada riwayat {{ strtolower($role) }} sebelumnya.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const assignmentStartDate = new Date('{{ $assignment->start_date }}');
            const assignmentEndDate = new Date('{{ $assignment->end_date }}');

            // Set min untuk input tanggal
            startDateInput.min = assignmentStartDate.toISOString().split('T')[0];
            endDateInput.min = assignmentStartDate.toISOString().split('T')[0];


            // Event listener untuk memastikan end_date tidak lebih awal dari start_date
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
            });

            endDateInput.addEventListener('change', function() {
                startDateInput.max = this.value;
            });
        });
    </script>
    @endpush
</x-app-layout>

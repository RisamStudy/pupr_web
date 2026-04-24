<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pekerjaan Selesai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Year Filter -->
            <div class="mb-6">
                <select id="year" name="year" onchange="changeYear(this.value)" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            Tahun {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pekerjaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($completedProjects as $project)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->project_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->alamat }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->completion_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('completed-projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Lihat</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                       {{ $completedProjects->appends(['year' => $selectedYear])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function changeYear(year) {
            // Ambil URL saat ini
            let currentUrl = new URL(window.location.href);
            let searchParams = new URLSearchParams(currentUrl.search);

            // Update parameter year
            searchParams.set('year', year);

            // Redirect ke URL baru
            window.location.href = `${currentUrl.pathname}?${searchParams.toString()}`;
        }
    </script>
</x-app-layout>
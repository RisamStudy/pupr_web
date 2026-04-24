@extends('layouts.operator-helper')

@section('header')
    <header class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-2xl text-white leading-tight">
                {{ __('Riwayat Pengerjaan') }}
            </h2>
        </div>
    </header>
@endsection

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Filter Section -->
            <div class="mb-6">
                <form action="{{ route('operator-helper.history') }}" method="GET" class="space-y-4 sm:flex sm:space-x-4 sm:space-y-0">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari Proyek</label>
                        <input type="text" name="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Nama proyek..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Semua</option>
                            <option value="Sedang Berlangsung" {{ request('status') == 'Sedang Berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Rentang Tanggal</label>
                        <input type="text" name="date_range" id="date_range" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="DD/MM/YYYY - DD/MM/YYYY" value="{{ request('date_range') }}">
                    </div>
                    <div class="self-end">
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Projects Table -->
            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                                Nama Proyek
                            </th>
                            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                                Tanggal Mulai
                            </th>
                            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                                Tanggal Selesai
                            </th>
                            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                                Status
                            </th>
                            <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workAssignments as $assignment)
                        <tr>
                            <td class="border-b border-gray-200 px-6 py-4">
                                {{ $assignment->project_name }}
                            </td>
                            <td class="border-b border-gray-200 px-6 py-4">
                                {{ $assignment->start_date->format('d/m/Y') }}
                            </td>
                            <td class="border-b border-gray-200 px-6 py-4">
                                {{ $assignment->end_date->format('d/m/Y') }}
                            </td>
                            <td class="border-b border-gray-200 px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->status == 'Sedang Berlangsung' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $assignment->status }}
                                </span>
                            </td>
                            <td class="border-b border-gray-200 px-6 py-4">
                                <a href="{{ route('operator-helper.history.detail', $assignment) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out">Lihat Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="border-b border-gray-200 px-6 py-4 text-center text-gray-500">
                                Tidak ada data riwayat pengerjaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $workAssignments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Custom styles can be added here */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "d/m/Y",
        });
    });
</script>
@endpush

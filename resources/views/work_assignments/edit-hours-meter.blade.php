<!-- resources/views/work-assignments/edit-hours-meter.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Hours Meter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('work-assignments.update-hours-meter', $workAssignment) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="start_hours_meter" class="block font-medium text-sm text-gray-700">Start Hours Meter</label>
                            <input type="number" name="start_hours_meter" id="start_hours_meter" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('start_hours_meter', $workAssignment->start_hours_meter) }}">
                            @error('start_hours_meter')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="end_hours_meter" class="block font-medium text-sm text-gray-700">End Hours Meter</label>
                            <input type="number" name="end_hours_meter" id="end_hours_meter" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('end_hours_meter', $workAssignment->end_hours_meter) }}">
                            @error('end_hours_meter')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="start_hours_meter_image" class="block font-medium text-sm text-gray-700">Start Hours Meter Image</label>
                            <img src="{{ Storage::url($workAssignment->start_hours_meter_image) }}" alt="End Hours Meter" class="mt-1 max-w-xs">
                        </div>

                        <div class="mb-4">
                            <label for="end_hours_meter_image" class="block font-medium text-sm text-gray-700">End Hours Meter Image</label>
                            <img src="{{ Storage::url($workAssignment->end_hours_meter_image) }}" alt="End Hours Meter" class="mt-1 max-w-xs">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest  active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Update Hours Meter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
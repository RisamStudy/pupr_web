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
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="start_hours_meter" class="block text-sm font-medium text-gray-700">Start Hours Meter</label>
                            <input type="number" step="0.01" name="start_hours_meter" id="start_hours_meter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>

                        <div class="mb-4">
                            <label for="start_hours_meter_image" class="block text-sm font-medium text-gray-700">Start Hours Meter Image</label>
                            <input type="file" name="start_hours_meter_image" id="start_hours_meter_image" class="mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="end_hours_meter" class="block text-sm font-medium text-gray-700">End Hours Meter</label>
                            <input type="number" step="0.01" name="end_hours_meter" id="end_hours_meter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>

                        <div class="mb-4">
                            <label for="end_hours_meter_image" class="block text-sm font-medium text-gray-700">End Hours Meter Image</label>
                            <input type="file" name="end_hours_meter_image" id="end_hours_meter_image" class="mt-1 block w-full" required>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Hours Meter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@extends('layouts.operator-helper')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-red-500 text-white px-6 py-4">
            <h1 class="text-2xl font-bold">Check Out - {{ $assignment->project_name }}</h1>
        </div>
        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">Ada beberapa masalah dengan input Anda:</span>
                    <ul class="mt-3 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <form action="{{ route('operator-helper.check-out', $assignment) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="mb-4">
                    <label for="check_out_photo" class="block text-sm font-medium text-gray-700">Foto Check Out (Maks. 2MB)</label>
                    <input type="file" id="check_out_photo" name="check_out_photo" accept="image/*" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    <div id="check_out_photo_preview" class="mt-2"></div>
                </div>
                <div class="mb-4">
                    <label for="hours_meter_end" class="block text-sm font-medium text-gray-700">Hours Meter Akhir</label>
                    <input type="number" id="hours_meter_end" name="hours_meter_end" step="0.1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" value="{{ old('hours_meter_end') }}">
                </div>
                <div class="mb-4">
                    <label for="hours_meter_end_photo" class="block text-sm font-medium text-gray-700">Foto Hours Meter Akhir (Maks. 2MB)</label>
                    <input type="file" id="hours_meter_end_photo" name="hours_meter_end_photo" accept="image/*" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    <div id="hours_meter_end_photo_preview" class="mt-2"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="text" id="latitude" name="latitude" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="text" id="longitude" name="longitude" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div>
                    <button type="button" onclick="getLocation()" class="mt-1 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Dapatkan Lokasi GPS
                    </button>
                </div>
                <div id="map" style="height: 300px;" class="mt-4 rounded-lg shadow-md"></div>
                <div class="mb-4">
                    <label for="field_condition" class="block text-sm font-medium text-gray-700">Kondisi Lapangan</label>
                    <textarea id="field_condition" name="field_condition" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
                <input type="hidden" id="location" name="location">
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Check Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        margin-top: 10px;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js"></script>
<script>
    let map, marker;

    function initMap() {
        map = L.map('map').setView([-6.200000, 106.816666], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© PU Cimancis'
        }).addTo(map);
    }

    function updateMap(lat, lng) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 13);
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
        updateMap(lat, lng);
        updateLocationField();
    }

    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }

    function updateLocationField() {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        document.getElementById('location').value = `${lat},${lng}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Listen for manual input changes
        document.getElementById('latitude').addEventListener('change', function() {
            updateMapFromInput();
            updateLocationField();
        });
        document.getElementById('longitude').addEventListener('change', function() {
            updateMapFromInput();
            updateLocationField();
        });

        function compressImage(file, callback) {
            new Compressor(file, {
                quality: 0.6,
                maxWidth: 1600,
                maxHeight: 1600,
                success(result) {
                    callback(result);
                },
                error(err) {
                    console.error('Compression error:', err);
                    callback(file);
                },
            });
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                compressImage(file, (compressedFile) => {
                    const compressedFileObject = new File([compressedFile], file.name, {
                        type: compressedFile.type,
                        lastModified: new Date().getTime()
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFileObject);
                    event.target.files = dataTransfer.files;

                    // Preview image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.getElementById(`${event.target.id}_preview`);
                        previewDiv.innerHTML = `<img src="${e.target.result}" alt="Preview" class="image-preview">`;
                    }
                    reader.readAsDataURL(compressedFileObject);
                });
            }
        }

        document.getElementById('check_out_photo').addEventListener('change', handleFileSelect);
        document.getElementById('hours_meter_end_photo').addEventListener('change', handleFileSelect);
    });

    function updateMapFromInput() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            updateMap(lat, lng);
        }
    }
</script>
@endpush

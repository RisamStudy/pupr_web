@extends('layouts.operator-helper')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Foto Kondisi Lapangan - {{ $workAssignment->project_name }}</h1>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2">Unggah Foto Baru</h2>
        @include('operator-helper.field-condition-photos.upload-form')
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xl font-semibold mb-2">Daftar Foto</h2>
            <div id="photoList" class="space-y-4">
                @foreach($photos as $photo)
                    @include('operator-helper.field-condition-photos.photo-item', ['photo' => $photo])
                @endforeach
            </div>
        </div>
        <div>
            <h2 class="text-xl font-semibold mb-2">Peta Lokasi</h2>
            <div id="map" class="h-96 rounded-lg shadow-md"></div>
            <p class="mt-4 text-lg">Total jarak penanganan: <span id="totalDistance">{{ number_format($totalDistance, 2) }}</span> km</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .treatment-point { background-color: red; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; }
    .non-treatment-point { background-color: blue; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // JavaScript untuk menginisialisasi peta dan menambahkan marker akan ditambahkan di sini
    let map;
    let markers = [];
    let polyline;

    function initMap() {
        map = L.map('map').setView([-6.200000, 106.816666], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; PU Cimancis'
        }).addTo(map);
        polyline = L.polyline([], {color: 'red'}).addTo(map);

        // Add existing markers
        @foreach($photos as $photo)
            addMarker({{ $photo->latitude }}, {{ $photo->longitude }}, {{ $photo->is_treatment_point ? 'true' : 'false' }});
        @endforeach

        updatePath();
    }

    function addMarker(lat, lng, isTreatmentPoint) {
        const marker = L.marker([lat, lng], {
            icon: L.divIcon({className: isTreatmentPoint ? 'treatment-point' : 'non-treatment-point'})
        }).addTo(map);
        markers.push({marker: marker, isTreatmentPoint: isTreatmentPoint});
    }

    function updatePath() {
        const treatmentPoints = markers.filter(m => m.isTreatmentPoint).map(m => m.marker.getLatLng());
        polyline.setLatLngs(treatmentPoints);
        map.fitBounds(polyline.getBounds());
    }

    initMap();
</script>
@endpush

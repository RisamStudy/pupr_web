<div class="bg-white shadow-md rounded-lg overflow-hidden" id="photo-{{ $photo->id }}">
    <img src="{{ Storage::url($photo->photo_path) }}" alt="Field Condition Photo" class="w-full h-48 object-cover">
    <div class="p-4">
        <p class="text-sm text-gray-600">Latitude: {{ $photo->latitude }}</p>
        <p class="text-sm text-gray-600">Longitude: {{ $photo->longitude }}</p>
        <p class="text-sm text-gray-600">Titik Penanganan: {{ $photo->is_treatment_point ? 'Ya' : 'Tidak' }}</p>
        <button onclick="deletePhoto({{ $photo->id }})" class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">
            Hapus
        </button>
    </div>
</div>

<script>
function deletePhoto(photoId) {
    if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
        fetch(`{{ url('field-condition-photos') }}/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`photo-${photoId}`).remove();
                // Remove marker from map
                const markerIndex = markers.findIndex(m => m.marker.getLatLng().lat === {{ $photo->latitude }} && m.marker.getLatLng().lng === {{ $photo->longitude }});
                if (markerIndex > -1) {
                    map.removeLayer(markers[markerIndex].marker);
                    markers.splice(markerIndex, 1);
                }
                updatePath();
                document.getElementById('totalDistance').textContent = data.total_distance.toFixed(2);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>

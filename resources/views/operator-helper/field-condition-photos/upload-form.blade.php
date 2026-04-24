<form id="uploadForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="photo">
            Foto
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="photo" type="file" name="photo" accept="image/*" capture="camera" required>
    </div>
    <div class="mb-4">
        <label class="flex items-center">
            <input type="checkbox" class="form-checkbox" name="is_treatment_point" value="1" checked>
            <span class="ml-2 text-sm text-gray-700">Titik Penanganan</span>
        </label>
    </div>
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">
    <div class="flex items-center justify-between">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
            Unggah Foto
        </button>
    </div>
</form>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            const formData = new FormData(e.target);

            fetch('{{ route("field-condition-photos.store", $workAssignment) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addPhotoToList(data.photo);
                    addMarker(lat, lng, formData.get('is_treatment_point') === '1');
                    updatePath();
                    document.getElementById('totalDistance').textContent = data.total_distance.toFixed(2);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    } else {
        alert('Geolocation is not supported by your browser');
    }
});

function addPhotoToList(photo) {
    const photoList = document.getElementById('photoList');
    const photoElement = document.createElement('div');
    photoElement.innerHTML = `
        @include('operator-helper.field-condition-photos.photo-item', ['photo' => $photo])
    `.trim();
    photoList.prepend(photoElement.firstChild);
}
</script>

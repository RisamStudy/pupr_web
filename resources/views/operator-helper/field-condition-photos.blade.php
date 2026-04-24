@extends('layouts.operator-helper')

@section('content')
<div x-data="{ showDeleteModal: false, photoToDelete: null }" class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('operator-helper.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-green-500 text-white px-6 py-4">
            <h1 class="text-2xl font-bold">Foto Kondisi Lapangan - {{ $workAssignment->project_name }}</h1>
        </div>

        <div class="p-6">

            <div id="alert-container">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md flex items-center justify-between alert-dismissible" role="alert">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>{{ session('success') }}</p>
                        </div>
                        <button class="text-green-700 hover:text-green-900 close-alert">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md flex items-center justify-between alert-dismissible" role="alert">
                        <div>
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="font-bold">Terjadi kesalahan:</p>
                            </div>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button class="text-red-700 hover:text-red-900 close-alert">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <form action="{{ route('field-condition-photos.store', $workAssignment) }}" method="POST" enctype="multipart/form-data" id="upload-form" class="mb-8">
                @csrf
                <div class="mb-4">
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Foto (Bisa lebih dari satu)
                    </label>
                    <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                </div>
                <div id="image-preview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4"></div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Unggah Foto
                </button>
            </form>

            <hr class="my-8">

            <h3 class="font-semibold text-lg mb-4">Foto Kondisi Lapangan Saat Ini</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($fieldConditionPhotos as $photo)
                    <div class="relative">
                        <img src="{{ asset($photo->photo_path) }}" alt="Field Condition" class="w-full h-48 object-cover rounded-lg">
                        <button @click="showDeleteModal = true; photoToDelete = {{ $photo->id }}" class="absolute top-0 right-0 m-2 bg-red-500 hover:bg-red-700 text-white font-bold w-6 h-6 rounded-full flex items-center justify-center">
                            X
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-delete-modal show="showDeleteModal">
        <x-slot name="content">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Hapus Foto
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <form :action="'{{ route('field-condition-photos.destroy', '') }}/' + photoToDelete" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Hapus
                </button>
            </form>
            <button @click="showDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Batal
            </button>
        </x-slot>
    </x-delete-modal>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('photos');
    const preview = document.getElementById('image-preview');
    const form = document.getElementById('upload-form');

    input.addEventListener('change', updateImagePreview);

    function updateImagePreview() {
        preview.innerHTML = '';
        const files = input.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Image preview" class="w-full h-48 object-cover rounded-lg">
                        <button type="button" class="absolute top-0 right-0 m-2 bg-red-500 hover:bg-red-700 text-white font-bold w-6 h-6 rounded-full flex items-center justify-center" onclick="removeImage(this, ${i})">
                            X
                        </button>
                    `;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            }
        }
    }

    window.removeImage = function(button, index) {
        const dt = new DataTransfer();
        const { files } = input;
        for (let i = 0; i < files.length; i++) {
            if (index !== i)
                dt.items.add(files[i]);
        }
        input.files = dt.files;
        button.closest('.relative').remove();
    }

    form.addEventListener('submit', function(e) {
        if (input.files.length === 0) {
            e.preventDefault();
            alert('Silakan pilih setidaknya satu foto untuk diunggah.');
        }
    });

    // Alert dismissal
    const alertContainer = document.getElementById('alert-container');
    alertContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('close-alert') || e.target.closest('.close-alert')) {
            const alert = e.target.closest('.alert-dismissible');
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        });
    }, 5000);
});
</script>

<style>
.alert-dismissible {
    transition: opacity 0.3s ease-in-out;
}
</style>
@endpush

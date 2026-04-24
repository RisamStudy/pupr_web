<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">There were some problems with your input.</span>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select name="status" id="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                    <option value="tersedia"
                                        {{ $user->status == 'tersedia' ? 'selected' : '' }}>Tersedia
                                    </option>
                                    <option value="bertugas" {{ $user->status == 'bertugas' ? 'selected' : '' }}>
                                        Bertugas</option>
                                    <option value="tidak ada"
                                        {{ $user->status == 'tidak ada' ? 'selected' : '' }}>Tidak Ada
                                    </option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                <small class="text-gray-500">Leave blank to keep the same password.</small>
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                            <div>
                                <x-input-label for="roles" :value="__('Roles')" />
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox" name="roles[]" value="operator" {{ in_array('operator', old('roles', $user->roles ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-2">Operator</span>
                                    </label>
                                    <label class="inline-flex items-center ml-0 sm:ml-6">
                                        <input type="checkbox" class="form-checkbox" name="roles[]" value="helper" {{ in_array('helper', old('roles', $user->roles ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-2">Helper</span>
                                    </label>
                                </div>
                            </div>
                            <div id="operatorField" style="{{ in_array('operator', old('roles', $user->roles ?? [])) ? 'display: block;' : 'display: none;' }}">
                                <x-input-label for="operator_types" :value="__('Operator Types')" />
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox" name="types[]" value="mekanik_alat_berat" {{ in_array('mekanik_alat_berat', old('types', $user->types ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-2">Mekanik Alat Berat</span>
                                    </label>
                                    <label class="inline-flex items-center ml-0 sm:ml-6">
                                        <input type="checkbox" class="form-checkbox" name="types[]" value="operator_alat_berat" {{ in_array('operator_alat_berat', old('types', $user->types ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-2">Operator Alat Berat</span>
                                    </label>
                                </div>
                            </div>
                            <div id="helperField" style="{{ in_array('helper', old('roles', $user->roles ?? [])) ? 'display: block;' : 'display: none;' }}">
                                <x-input-label for="helper_types" :value="__('Helper Types')" />
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox" name="types[]" value="pembantu_mekanik" {{ in_array('pembantu_mekanik', old('types', $user->types ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-2">Pembantu Mekanik</span>
                                    </label>
                                    <label class="inline-flex items-center ml-0 sm:ml-6">
                                        <input type="checkbox" class="form-checkbox" name="types[]" value="pembantu_operator" {{ in_array('pembantu_operator', old('types', $user->types ?? [])) ? 'checked' : '' }}>
                                        <span class="ml-2">Pembantu Operator</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-button type="submit" color="indigo">
                                Update User
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
            var operatorField = document.getElementById('operatorField');
            var helperField = document.getElementById('helperField');

            function toggleFields() {
                var isOperator = document.querySelector('input[name="roles[]"][value="operator"]').checked;
                var isHelper = document.querySelector('input[name="roles[]"][value="helper"]').checked;

                operatorField.style.display = isOperator ? 'block' : 'none';
                helperField.style.display = isHelper ? 'block' : 'none';

                var operatorTypes = operatorField.querySelectorAll('input[type="checkbox"]');
                var helperTypes = helperField.querySelectorAll('input[type="checkbox"]');

                operatorTypes.forEach(function(checkbox) {
                    checkbox.disabled = !isOperator;
                    // Remove the required attribute
                    checkbox.required = false;
                });

                helperTypes.forEach(function(checkbox) {
                    checkbox.disabled = !isHelper;
                    // Remove the required attribute
                    checkbox.required = false;
                });
            }

            roleCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', toggleFields);
            });

            // Set initial state on page load
            toggleFields();
        });
    </script>
</x-app-layout>

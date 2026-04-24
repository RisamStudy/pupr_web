@extends('layouts.operator-helper')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                {{ __('Profile') }}
            </h2>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">
                    Profile has been updated successfully.
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('operator-helper.profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('operator-helper.profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('operator-helper.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection

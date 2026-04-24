@props(['disabled' => false, 'error' => ''])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md' .
        ($error ? ' border-red-500' : ''),
]) !!}>

@if ($error)
    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
@endif

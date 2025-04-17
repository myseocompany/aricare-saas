@props([
    'user' => filament()->auth()->user(),
])



@php
    $avatarUrl = $user->profile;
    $isFallback = str_contains($avatarUrl, 'user-avatar.png');
@endphp

@if (! $isFallback)
    <x-filament::avatar 
        :src="$avatarUrl"
        class="bg-white dark:bg-gray-800 ring-2 ring-primary-500 rounded-full"
/>
@else
    <x-heroicon-o-user 
        class="w-9 h-9 text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-full p-1"
/>
@endif



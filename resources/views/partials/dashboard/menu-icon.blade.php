@props(['name' => 'dashboard', 'class' => 'h-5 w-5'])

@switch($name)
    @case('academic-session')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
        </svg>
        @break

    @case('transactions')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h5M5 3h14a1 1 0 0 1 1 1v16l-3-2-3 2-3-2-3 2-3-2-3 2V4a1 1 0 0 1 1-1Z" />
        </svg>
        @break

    @case('cms')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 12h16M4 19h10M7 8h.01M7 15h.01" />
        </svg>
        @break

    @case('resources')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5v-17Z" />
        </svg>
        @break

    @case('election')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-5M5 9h14l-2 11H7L5 9Zm3 0V5a4 4 0 0 1 8 0v4" />
        </svg>
        @break

    @case('contest')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Zm10 2h3a3 3 0 0 1-3 3M7 6H4a3 3 0 0 0 3 3" />
        </svg>
        @break

    @case('members')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0M18 8a3 3 0 0 1 3 3M3 11a3 3 0 0 1 3-3" />
        </svg>
        @break

    @case('projects')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h10l4 4v12H5V4Zm10 0v5h5M8 13h8M8 17h6" />
        </svg>
        @break

    @case('levels')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16M6 16h3V8H6v8Zm5 0h3V4h-3v12Zm5 0h3v-6h-3v6Z" />
        </svg>
        @break

    @case('price-settings')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9a4 4 0 0 0-4-2H9.5a2.5 2.5 0 0 0 0 5H14a2.5 2.5 0 0 1 0 5h-2a4 4 0 0 1-4-2M4 12a8 8 0 1 0 16 0 8 8 0 0 0-16 0Z" />
        </svg>
        @break

    @case('admins')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3Zm0 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm-4 6a4 4 0 0 1 8 0" />
        </svg>
        @break

    @case('fees')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v10H4V7Zm3 3h.01M17 14h.01M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0Z" />
        </svg>
        @break

    @case('profile')
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 8a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21a8 8 0 0 1 16 0" />
        </svg>
        @break

    @default
        <svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-3H4v3Z" />
        </svg>
@endswitch

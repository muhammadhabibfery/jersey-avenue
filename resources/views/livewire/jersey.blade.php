<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('League Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __('List of :league jerseys', ['league' => $title ?: __('all leagues')]) }}
        </x-text-header>
    </x-slot>
</div>
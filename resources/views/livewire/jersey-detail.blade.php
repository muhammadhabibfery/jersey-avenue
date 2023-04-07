<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Jersey Detail Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __(':club jersey details', ['club' => $title]) }}
        </x-text-header>
    </x-slot>

    <section class="container min-h-screen py-12 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        <div class="box-content px-4">
            <p>Test gan</p>
        </div>
    </section>
</div>

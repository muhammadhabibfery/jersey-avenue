<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('League Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __('List of leagues') }}
        </x-text-header>
    </x-slot>

    <section class="container min-h-screen py-12 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        <div class="box-content px-4">
            <div class="md:flex md:justify-end">
                <x-text-input class="block w-full md:w-1/3" type="text" placeholder="{{ __('Search leagues ...') }}"
                    wire:model.debounce.500ms="name" />
            </div>

            <div class="box-border mt-5 md:flex md:flex-wrap md:gap-3">
                @forelse ($leagues as $league)
                <a href="{{ route('jersey', ['slug' => $league->slug]) }}"
                    class="box-border py-2 flex items-center justify-center w-full mb-3 overflow-y-hidden border-2 rounded-md border-slate-400 md:w-[23%] md:mb-0 h-[177px] md:h-[200px]">
                    <img src="{{ $league->getImage() }}" alt="{{ $league->name }}"
                        class="md:w-[75%] md:h-[75%] w-[50%]">
                </a>
                @empty
                <h3 class="w-full py-2 mt-10 font-mono text-lg italic text-center shadow-lg md:text-xl">
                    {{ __('League not available') }}
                </h3>
                @endforelse
            </div>

            <div class="justify-around w-full mx-auto text-center mt-7 md:mt-14 md:w-1/2 shadow-md">
                {{ $leagues->onEachSide(3)->links() }}
            </div>
        </div>
    </section>
</div>

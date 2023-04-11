<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Jersey Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __('List of :league jerseys', ['league' => $title ?: __('all leagues')]) }}
        </x-text-header>
    </x-slot>

    <section class="container min-h-screen py-12 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        <div class="box-content px-4">
            <div class="md:flex md:gap-4 md:justify-end">
                <select wire:model="leagueSelected" class="w-full mb-3 md:w-1/4 md:mb-0 border-gray-300 focus:border-indigo-500
focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="" {{ $leagueSelected ? 'disabled' : '' }}>{{ __('Choose league') }}</option>
                    <option value="">{{ __('all leagues') }}</option>
                    @foreach ($allLeagues as $slug => $name)
                    <option value="{{ $slug }}">{{ $name }}</option>
                    @endforeach
                </select>
                <x-text-input class="w-full md:w-1/3" type="text" placeholder="{{ __('Search jerseys ...') }}"
                    wire:model.debounce.500ms="name" />
            </div>

            <div class="box-border mt-5 md:flex md:flex-wrap md:gap-3 justify-around">
                @forelse ($jerseys as $jersey)
                <div
                    class="flex justify-center items-center border-2 border-slate-400 rounded-md p-7 mb-3 md:w-1/3 lg:w-1/4 md:max-w-[287px]">
                    <div>
                        <img src="{{ $jersey->getImage() }}" alt="{{ $jersey->name }}"
                            class="object-cover max-h-[220px] md:max-h-[171px] mx-auto md:h-[112px] lg:h-auto">
                        <h4 class="font-bold uppercase md:text-sm lg:text-base">{{ $jersey->name }}</h4>
                        <p class="md:text-xs lg:text-base">{{ currencyFormat($jersey->price) }}</p>
                        <x-primary-link href="{{ route('jersey.detail', $jersey) }}"
                            class="justify-center w-full mt-1 text-sm md:text-xs text-white bg-gray-700 rounded-md lg:text-xs">
                            {{ __('Detail') }}
                        </x-primary-link>
                    </div>
                </div>
                @empty
                <h3 class="w-full py-2 mt-10 font-mono text-lg italic text-center shadow-lg md:text-xl">
                    {{ __('Jersey not available') }}
                </h3>
                @endforelse
            </div>

            <div class="justify-around w-full mx-auto text-center mt-7 shadow-sm">
                {{ $jerseys->onEachSide(2)->links() }}
            </div>

        </div>
    </section>
</div>
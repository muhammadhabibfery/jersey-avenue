<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }}
    </x-slot>

    <!-- Hero Image -->
    <section id="hero-image" class="bg-home h-[100vmin] flex justify-center items-center">
        <div class="container bg-transparent/50 w-10/12 p-5 shadow-md shadow-slate-300 rounded-sm md:h-[50vh] md:flex">
            <span class="m-auto md:w-10/12">
                <!-- Session Status -->
                <x-auth-session-status :status="session('status')" class="mb-6" />

                <h3 class="mb-2 text-3xl text-white md:text-center shadow-danger-900 md:text-7xl md:mb-6">
                    Jersey Avenue
                </h3>
                <p class="text-sm text-slate-300 md:text-2xl md:text-center">
                    Wear Your Passion on Your Sleeve
                </p>
            </span>
        </div>
    </section>

    <!-- Top Leagues -->
    <section id="top-leagues" class="px-5 mt-10">
        <div class="container mx-auto">
            <div class="flex items-center justify-between">
                <h3 class="text-lg md:text-xl">{{ __('Choose league') }}</h3>
                <x-primary-link href="{{ route('league') }}" class="mt-1 text-sm text-white bg-gray-700 rounded-md">
                    {{ __('See all') }}
                </x-primary-link>
            </div>
            <div class="w-full mt-4 md:flex md:gap-5 md:justify-center">
                @foreach ($topLeagues as $topLeague)
                <a href="{{ route('jersey', ['slug' => $topLeague->slug]) }}"
                    class="flex justify-center items-center border-2 border-slate-400 rounded-md p-7 overflow-y-hidden h-[280px] mb-3 md:w-1/4 md:max-w-[287px]">
                    <img src="{{ $topLeague->getImage() }}" alt="{{ $topLeague->name }}" class="object-cover">
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Top Jerseys -->
    <section id="top-jerseys" class="px-5 mt-10">
        <div class="container mx-auto">
            <div class="flex items-center justify-between">
                <h3 class="text-lg md:text-xl">{{ __('Best Seller') }}</h3>
                <x-primary-link href="{{ route('jersey') }}" class="mt-1 text-sm text-white bg-gray-700 rounded-md">
                    {{ __('See all') }}
                </x-primary-link>
            </div>
            <div class="w-full mt-4 text-center md:flex md:gap-5 md:justify-center">
                @foreach ($topJerseys as $topJersey)
                <div
                    class="flex justify-center items-center border-2 border-slate-400 rounded-md p-7 mb-3 md:w-1/4 md:max-w-[287px]">
                    <div>
                        <img src="{{ $topJersey->getImage() }}" alt="{{ $topJersey->name }}"
                            class="object-cover max-h-[220px] md:max-h-[171px] mx-auto">
                        <h4 class="font-bold uppercase">{{ $topJersey->name }}</h4>
                        <p>{{ currencyFormat($topJersey->price) }}</p>
                        <x-primary-link href="{{ route('jersey.detail', $topJersey) }}"
                            class=" w-full justify-center mt-1 text-sm text-white bg-gray-700 rounded-md">
                            {{ __('Detail') }}
                        </x-primary-link>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Jersey Detail Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __(':club jersey details', ['club' => $title]) }}
        </x-text-header>
        <x-primary-link href="{{ route('jersey') }}" class="mt-3 text-sm text-white bg-gray-700 rounded-md">
            {{ __('Back') }}
        </x-primary-link>
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" class="container w-[90%] flex justify-center mx-auto mt-4" />

    <section class="container min-h-screen py-12 mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8 flex justify-center">
        <div class="box-content px-4 md:w-[50%]">
            <div class="border border-gray-400 shadow-md shadow-stone-500">
                <div class="bg-slate-200">
                    <img src="{{ $jersey->getImage() }}" alt="{{ $jersey->name }}" class="object-cover mx-auto">
                </div>
                <div class="p-2 md:p-4">
                    <h4 class="font-bold uppercase lg:text-lg">{{ $jersey->name }}</h4>
                    <p class="font-serif font-semibold">{{ currencyFormat($jersey->price) }}</p>
                    <table class="table-auto  w-full">
                        <tr>
                            <td>{{ __('League') }}</td>
                            <td>:</td>
                            <td>{{ $jersey->league->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Type') }}</td>
                            <td>:</td>
                            <td>{{ $jersey->type }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Weight') }}</td>
                            <td>:</td>
                            <td>{{ $jersey->weight }} gram</td>
                        </tr>
                        <tr>
                            <td>{{ __('Stock') }}</td>
                            <td>:</td>
                            <td>
                                <div class="flex">
                                    @foreach ($jersey->stock as $key => $stock)
                                        <span class="w-[25%]">{{ $key }} : {{ $stock }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-top">{{ __('Quantity') }}</td>
                            <td class="align-top">:</td>
                            <td>
                                <div class="flex justify-between flex-wrap">
                                    <select
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mb-1 w-[49%]"
                                        wire:model="size">
                                        <option value="" {{ $size ? 'disabled' : '' }}>{{ __('Choose size') }}</option>
                                        @foreach ($jersey->stock as $key => $stock)
                                            @if ($stock > 0)
                                                <option value="{{ $key }}">{{ $key }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <x-text-input class="w-[49%] h-[42px]" type="number"
                                        placeholder="{{ __('Quantity') }}" wire:model.lazy="quantity" />
                                </div>
                                <div class="flex flex-wrap">
                                    <div class="w-[50%] md:pl-1 pr-1">
                                        @error('size') <small class="text-red-500 font-bold">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="w-[50%] pl-1 md:pl-2">
                                        @error('quantity') <small class="text-red-500 font-bold">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    @if ($size && $quantity)
                        <p class="font-semibold mt-3 text-lg">{{ __('Nameset') }} {{ currencyFormat($jersey->price_nameset)
                            }}</p>
                        <small class="italic">({{ __('Fill the form below if you want to use nameset') }})</small>
                        <table class="table-auto w-full">
                            @foreach ($nameset as $key => $ns)
                                <tr>
                                    <td class="align-top">{{ __('Name') }}</td>
                                    <td class="align-top">:</td>
                                    <td>
                                        <x-text-input class="w-full" type="text" placeholder="{{ __('Name') }}"
                                            wire:model.lazy="nameset.{{ $key }}.name" />
                                        @error("nameset.$key.name") <small class="text-red-500 ml-1 font-bold">{{ $message
                                            }}</small>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-top">{{ __('Number') }}</td>
                                    <td class="align-top">:</td>
                                    <td>
                                        <x-text-input class="w-full" type="number" placeholder="{{ __('Number') }}"
                                            wire:model.lazy="nameset.{{ $key }}.number" />
                                        @error("nameset.$key.number") <small class="text-red-500 ml-1 font-bold">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <x-primary-link class="bg-danger-600 text-white mt-1 w-full flex justify-center"
                                            wire:click.prevent="removeNameset({{ $key }})">
                                            {{ __('Delete') }}
                                        </x-primary-link>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        @if ($quantity > 1 && count($nameset) < $quantity)
                            <div class="flex flex-wrap justify-center">
                                <x-primary-link class="mt-3 text-sm text-white bg-blue-950 rounded-md w-full"
                                    wire:click.prevent="addMoreNameset">
                                    <small class="font-light font-sans">{{ __('Add more nameset') }}</small>
                                </x-primary-link>
                                @error("addMoreNameset") <small class="text-red-500 ml-1 font-bold w-full text-center">{{ $message }}</small>
                                @enderror
                            </div>
                    @endif
                @endif
                @if ($quantity && $size)
                    <x-primary-button
                        class="mt-5 text-sm text-white bg-gray-700 rounded-md w-full mx-auto flex justify-center"
                        :livewireButton="true" :request="true" wire:click.prevent="addToCart">
                        🛒 {{ __('Add to cart') }}
                    </x-primary-button>
                @endif
            </div>
        </div>
    </section>
</div>

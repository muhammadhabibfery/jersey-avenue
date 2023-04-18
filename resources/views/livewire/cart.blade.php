<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Shopping Cart Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __('Shopping Cart') }}
        </x-text-header>
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" class="container w-[75%] flex justify-center mx-auto mt-4" />

    <section class="container flex flex-wrap px-2 py-12 mx-auto md:justify-between max-w-7xl sm:px-6 lg:px-8">
        <div class="w-full md:w-[68%]">
            <div class="box-content p-2 overflow-x-scroll shadow-md shadow-stone-500">
                <h3 class="py-3 pl-1 mb-2 text-lg font-semibold border-b border-slate-300">{{ __('Your Jersey list') }}
                </h3>
                <table class="mx-auto font-light text-center table-auto">
                    <thead class="text-sm font-medium border-b bg-neutral-50 lg:text-base">
                        <tr>
                            <th scope="col" class="px-3 py-4">No.</th>
                            <th scope="col" class="px-3 py-4">{{ __('Jerseys') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Name') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Nameset') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Size') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Quantity') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Price') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Total price') }}</th>
                            <th scope="col" class="px-3 py-4">{{ __('Delete') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs lg:text-sm">
                        @if ($order)
                        @forelse ($order->jerseys as $jersey)
                        <tr class="border-b">
                            <td class="px-3 py-4 font-medium whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <img src="{{ $jersey->getImage() }}" alt="{{ $jersey->name }}"
                                    class="object-cover mx-auto">
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ $jersey->name }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                @if (count(json_decode($jersey->pivot->nameset)) > 0)
                                <ul class="list-item">
                                    @foreach (json_decode($jersey->pivot->nameset) as $nameset)
                                    <li>{{ $nameset->name }} {{ $nameset->number }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-xl font-bold">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ $jersey->pivot->size }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ $jersey->pivot->quantity }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ currencyFormat($jersey->price) }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ currencyFormat($jersey->pivot->total_price) }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <a href="#"
                                    class="text-lg cursor-pointer text-danger-600 hover:text-danger-700 hover:text-2xl"
                                    wire:click.prevent="removeJersey({{ $jersey->pivot }})">
                                    X
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr class="border-b">
                            <td colspan="8"
                                class="px-3 py-4 font-serif italic font-medium text-left whitespace-nowrap md:text-center">
                                {{ __('Not available')
                                }}</td>
                        </tr>
                        @endforelse
                        @else
                        <tr class="border-b">
                            <td colspan="8"
                                class="px-3 py-4 font-serif italic font-medium text-left whitespace-nowrap md:text-center">
                                {{ __('Not available')
                                }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($order && count($order->jerseys) > 0)
            <div class="box-content p-2 mt-6 overflow-x-scroll shadow-md shadow-stone-500">
                <h3 class="py-3 pl-1 mb-2 text-lg font-semibold border-b border-slate-300">
                    {{ __('Shipping address form') }}
                </h3>
                <table class="text-sm table-auto">
                    <tr class="border-b ">
                        <td class="py-4 pl-6 pr-4 font-medium whitespace-nowrap">{{ __('Phone') }}</td>
                        <td class="px-2 py-4 font-medium whitespace-nowrap">:</td>
                        <td class="flex flex-wrap py-4 font-medium whitespace-nowrap">
                            <x-text-input class="w-full md:w-[38vw] lg:w-[45vw]" type="number"
                                wire:model.lazy="phone" />
                            @error("phone") <small class="mt-1 ml-1 font-bold text-red-500">{{ $message
                                }}</small>
                            @enderror
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pl-6 pr-4 font-medium whitespace-nowrap">{{ __('Province') }}</td>
                        <td class="px-2 py-4 font-medium whitespace-nowrap">:</td>
                        <td class="flex flex-wrap py-4 font-medium whitespace-nowrap">
                            <select
                                class="w-full md:w-[38vw] lg:w-[45vw] border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                wire:model="selectedProvince">
                                <option value="" {{ $selectedProvince ? 'disabled' : '' }}>{{ __('Choose province') }}
                                </option>
                                @foreach ($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                            @error("selectedProvince") <small class="mt-1 ml-1 font-bold text-red-500">{{ $message
                                }}</small>
                            @enderror
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pl-6 pr-4 font-medium whitespace-nowrap">{{ __('City') }}</td>
                        <td class="px-2 py-4 font-medium whitespace-nowrap">:</td>
                        <td class="flex flex-wrap py-4 font-medium whitespace-nowrap">
                            <select
                                class="w-full md:w-[38vw] lg:w-[45vw] border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                wire:model="selectedCity">
                                <option value="" {{ $selectedCity ? 'disabled' : '' }}>{{ __('Choose city') }}</option>
                                @if ($cities)
                                @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            @error("selectedCity") <small class="mt-1 ml-1 font-bold text-red-500">{{ $message
                                }}</small>
                            @enderror
                        </td>
                    </tr>
                    <tr class="border-b ">
                        <td class="py-4 pl-6 pr-4 font-medium whitespace-nowrap">{{ __('Address') }}</td>
                        <td class="px-2 py-4 font-medium whitespace-nowrap">:</td>
                        <td class="flex flex-wrap py-4 font-medium whitespace-nowrap">
                            <textarea
                                class="w-full md:w-[38vw] lg:w-[45vw] border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                wire:model.lazy="address"></textarea>
                            @error("address") <small class="mt-1 ml-1 font-bold text-red-500">{{ $message
                                }}</small>
                            @enderror
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pl-6 pr-4 font-medium whitespace-nowrap">{{ __('Courier Services') }}</td>
                        <td class="px-2 py-4 font-medium whitespace-nowrap">:</td>
                        <td class="flex flex-wrap py-4 font-medium whitespace-nowrap">
                            <select
                                class="w-full md:w-[38vw] lg:w-[45vw] border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                wire:model="selectedCourier">
                                <option value="" {{ $selectedCourier ? 'disabled' : '' }}>
                                    {{ __('Choose courier services') }}
                                </option>
                                @foreach ($couriers as $courier)
                                <option value="{{ $courier['id'] }}">{{ $courier['name'] }}</option>
                                @endforeach
                            </select>
                            @error("selectedCourier") <small class="mt-1 ml-1 font-bold text-red-500">{{ $message
                                }}</small>
                            @enderror
                            @if ($courierCosts)
                            <select
                                class="w-full mt-2 md:w-[38vw] lg:w-[45vw] border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                wire:model="selectedCourierCost">
                                <option value="" {{ $selectedCourierCost ? 'disabled' : '' }}>
                                    {{ __('Choose courier service costs') }}
                                </option>
                                @foreach ($courierCosts['costs'] as $cost)
                                <option value="{{ $courierCosts['code'] }},{{ $cost['service'] }},{{ $cost['value'] }}">
                                    <p>{{ strtoupper($courierCosts['code']) }} - {{ $cost['service'] }}
                                    </p>
                                    <p>{{ $cost['description'] }}</p>
                                    <p>{{ currencyFormat($cost['value']) }}</p>
                                    <p>{{ __('etd') }} : {{ $cost['etd'] }}</p>
                                </option>
                                @endforeach
                            </select>
                            @error("selectedCourierCost") <small class="mt-1 ml-1 font-bold text-red-500">{{ $message
                                }}</small>
                            @enderror
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @endif
        </div>

        @if ($order && count($order->jerseys) > 0 && $selectedCourierCost)
        <div class="w-full md:w-[30%] mt-9 md:mt-0">
            <div class="box-content p-2 overflow-x-auto shadow-md shadow-stone-500">
                <h3 class="py-3 pl-1 mb-2 text-lg font-semibold border-b border-slate-300">{{ __('Total') }}
                </h3>
                <table class="text-sm table-auto lg:text-base">
                    <tr class="border-b">
                        <td class="py-4 pl-2 pr-2 font-semibold whitespace-nowrap">{{ __('Sub Total') }}
                        </td>
                        <td class="py-4 font-medium whitespace-nowrap">:</td>
                        <td class="py-4 pl-1 font-medium whitespace-nowrap">
                            <p>{{ currencyFormat($order->total_price) }}</p>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pl-2 pr-2 font-semibold whitespace-nowrap">{{ __('Courier services cost') }}
                        </td>
                        <td class="py-4 font-medium whitespace-nowrap">:</td>
                        <td class="py-4 pl-1 font-medium whitespace-nowrap">
                            <p>{{ currencyFormat((int) last(explode(',', $selectedCourierCost))) }}</p>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-4 pl-2 pr-2 font-semibold whitespace-nowrap">{{ __('Total Price') }}
                        </td>
                        <td class="py-4 font-medium whitespace-nowrap">:</td>
                        <td class="py-4 pl-1 font-medium whitespace-nowrap">
                            <p>{{ currencyFormat($order->total_price + (int) last(explode(',', $selectedCourierCost)))
                                }}</p>
                        </td>
                    </tr>
                </table>

                <x-primary-button class="flex justify-center w-full mx-auto mt-6 text-white bg-gray-700 rounded-md"
                    :livewireButton="true" :request="true" wire:click.prevent="checkout">
                    💳 {{ __('Checkout') }}
                </x-primary-button>
            </div>
        </div>
        @endif

    </section>
</div>
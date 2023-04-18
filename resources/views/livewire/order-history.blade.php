<div>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Order History Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __('Order History') }}
        </x-text-header>
    </x-slot>

    <section class="container mx-auto px-2 py-12 max-w-7xl sm:px-6 lg:px-8 relative">
        <div class="w-full">
            <div class="flex mb-1 justify-end w-full md:mb-4">
                <select wire:model="selectedStatus" class="w-full mb-3 md:w-1/3 md:mb-0 border-gray-300 focus:border-indigo-500
            focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('All status') }}</option>
                    @foreach ($status as $s)
                    @continue($s == 'IN CART')
                    <option value="{{ $s }}">{{ ucwords(strtolower($s)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="box-content flex justify-center p-2 shadow-md shadow-stone-500">
                <div class="overflow-x-auto">
                    <h3 class="py-3 pl-1 mb-2 text-lg font-semibold border-b border-slate-300">
                        {{ __('Your Order History') }}
                    </h3>
                    <table class="mx-auto font-light text-center table-auto">
                        <thead class="text-sm font-medium border-b bg-neutral-50 lg:text-base">
                            <tr>
                                <th scope="col" class="px-3 py-4">No.</th>
                                <th scope="col" class="px-3 py-4">{{ __('Invoice Number') }}</th>
                                <th scope="col" class="px-3 py-4">{{ __('Courier Services') }}</th>
                                <th scope="col" class="px-3 py-4">{{ __('status') }}</th>
                                <th scope="col" class="px-3 py-4">{{ __('Total price') }}</th>
                                <th scope="col" class="px-3 py-4">{{ __('Detail') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs lg:text-sm">
                            @forelse ($orders as $order)
                            <tr class="border-b">
                                <td class="px-3 py-4 font-medium whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="px-3 py-4 whitespace-nowrap">{{ $order->invoice_number }}</td>
                                <td class="px-3 py-4 whitespace-nowrap">{{
                                    courierServiceFormat($order->courier_services) }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap font-serif">
                                    <span class="{{ $order->status_badge }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap font-semibold">{{
                                    currencyFormat($order->total_price)
                                    }}</td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <button type="button" class="cursor-pointer text-gray-600 hover:text-gray-800"
                                        wire:click="orderDetail({{ true }}, {{ $order->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
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
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="justify-around w-full mx-auto text-center mt-7 shadow-sm">
                {{ $orders->onEachSide(2)->links() }}
            </div>
        </div>

        <!-- Detail order modals -->
        @if ($modalButton)
        <div
            class="box-content flex justify-center shadow-md shadow-stone-500 z-50 absolute top-[50%] -translate-y-[50%] left-[50%] -translate-x-[50%] bg-white/70 backdrop-blur-md p-4 w-[90%] mx-auto max-h-[70%]">
            <div class="overflow-auto">
                <div class="flex justify-between w-full">
                    <h3 class="py-3 pl-1 mb-2 text-lg font-semibold border-b border-slate-300">
                        {{ __('Order details') }}
                    </h3>
                    <button type="button" class="text-lg pb-1" wire:click="orderDetail">✖</button>
                </div>
                <ol class="list-decimal">
                    @foreach ($jerseys as $jersey)
                    <li class="px-2 py-4 flex items-center">
                        <span class="w-[20%] pr-2">
                            <img src="{{ $jersey->getImage() }}" alt="{{ $jersey->name }}"
                                class="object-cover mx-auto md:w-[60%] lg:w-[50%]">
                        </span>
                        <div class="flex w-[80%]">
                            <div class="px-1 md:px-2">
                                <h3 class="font-semibold text-sm lg:text-base">{{ __('Name') }}:
                                </h3>
                                <span class="text-xs lg:text-sm">{{ $jersey->name }}</span>
                            </div>
                            <div class="px-1">
                                <h3 class="font-semibold text-sm lg:text-base">{{ __('Nameset') }}:
                                </h3>
                                @forelse (json_decode($jersey->pivot->nameset) as $nameset)

                                <p class="text-xs lg:text-sm">{{ $nameset->name }} {{
                                    $nameset->number }}</p>
                                @empty
                                <span class="text-xs lg:text-sm font-semibold flex justify-center">-</span>
                                @endforelse
                            </div>
                            <div class="px-1 md:px-2">
                                <h3 class="font-semibold text-sm lg:text-base">{{ __('Size') }}:
                                </h3>
                                <span class="text-xs lg:text-sm flex justify-center">
                                    {{ $jersey->pivot->size }}
                                </span>
                            </div>
                            <div class="px-1 md:px-2">
                                <h3 class="font-semibold text-sm lg:text-base">{{ __('Quantity') }}:
                                </h3>
                                <span class="text-xs lg:text-sm flex justify-center">
                                    {{ $jersey->pivot->quantity }}
                                </span>
                            </div>
                            <div class="px-1 md:px-2">
                                <h3 class="font-semibold text-sm lg:text-base">{{ __('Price') }}:
                                </h3>
                                <span class="text-xs lg:text-sm">{{ currencyFormat($jersey->price)
                                    }}</span>
                            </div>
                            <div class="px-1 md:px-2">
                                <h3 class="font-semibold text-sm lg:text-base">{{ __('Total price')
                                    }}:</h3>
                                <span class="text-xs lg:text-sm">
                                    {{ currencyFormat($jersey->pivot->total_price) }}
                                </span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
        @endif
    </section>
</div>
@props(['status'])

@if ($status)
<div {{ $attributes->merge(['class' => 'p-3 my-2 text-center text-white rounded-md shadow-lg bg-green-500/75 relative'])
    }} :class="clicked ? 'hidden' : ''" x-data="{clicked: false}">
    <div class="inline-block w-10/12 pr-1 overflow-y-scroll max-h-16">
        @if (is_array($status))
        <div class="text-xs md:text-lg">
            <span>{{ $status['title'] }}</span>
        </div>
        <div class="text-xs md:text-sm">
            <div>
                @isset($status['icon'])
                <span class="mr-2">{{ $status['icon'] }}</span>
                @endisset
                <span>{{ $status['paragraph'] }}</span>
            </div>
        </div>
        @else
        <div class="text-xs md:text-lg">
            <span>{{ $status }}</span>
        </div>
        @endif
    </div>
    <span @click="clicked=true"
        class="absolute text-xs cursor-pointer text-righ md:text-lg md:right-3 right-1 top-[50%] -translate-y-[50%]">✖</span>
</div>
@endif
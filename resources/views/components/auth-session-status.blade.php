@props(['status'])

@if ($status)
<div class="p-3 my-2 text-center text-white rounded-md shadow-lg bg-green-500/75 ">
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
@endif

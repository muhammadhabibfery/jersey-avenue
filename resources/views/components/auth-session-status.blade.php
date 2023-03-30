@props(['status'])

@if ($status)
<div class="p-3 my-2 text-center text-white rounded-md shadow-lg bg-green-500/75 ">
    <div class="text-xs md:text-lg">
        <span>{{ session('status')['title'] }}</span>
    </div>
    <div class="text-xs md:text-sm">
        <div>
            <span class="mr-2">📩</span>
            <span>{{ session('status')['paragraph'] }}</span>
        </div>
    </div>
</div>
@endif

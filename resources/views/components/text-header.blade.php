<span x-data="{open: false}" class="cursor-pointer">
    <h1 {{ $attributes->merge(['class' => 'text-xl font-semibold leading-tight text-gray-800 sm:text-lg md:text-2xl'])
        }}>
        {{ $slot }}
    </h1>
</span>
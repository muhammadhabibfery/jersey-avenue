@props(['description'])

<div class="block mb-3">
    <x-social-link href="{{ route('auth.redirect.google') }}"
        class="justify-center w-full my-2 text-center bg-red-600 hover:bg-red-700">
        <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
            class="inline-block fill-current w-[6%] md:w-[3%] -mt-[2px] md:-mt-1">
            <title>Google</title>
            <path
                d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z" />
        </svg>
        <span class="pl-2">
            {{ __("$description with google") }}
        </span>
    </x-social-link>
</div>

<div class="flex mb-5 text-white">
    <small class="border-t-2 mt-3 border-white w-[50%]"></small>
    <p class="px-3">{{ __('or') }}</p>
    <small class="border-t-2 mt-3 border-white w-[50%]"></small>
</div>
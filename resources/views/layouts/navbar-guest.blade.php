<div>
    <div class="fixed z-50 w-full p-6 text-right bg-white border-b border-gray-200 sm:fixed sm:top-0 sm:right-0">
        <div class="container flex justify-between px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <a href="{{ route('home') }}">
                <span class="font-serif text-bold font-weight-bold">Jersey Avenue</span>
            </a>

            <div class="">
                <a href="{{ route('login') }}"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">{{
                    __('Login') }}</a>

                <a href="{{ route('register') }}"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">{{
                    __('Register') }}</a>
            </div>
        </div>
    </div>
</div>

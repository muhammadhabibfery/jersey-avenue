<div class="px-4">
    <form wire:submit.prevent='save'>
        {{ $this->form }}

        <x-primary-button class="justify-center w-full py-3 mt-7" :livewireButton="true">
            {{ __('Update') }}
        </x-primary-button>
    </form>
</div>

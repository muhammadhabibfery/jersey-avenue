<?php

namespace App\Http\Livewire;

use Livewire\Component;

class NavbarAuth extends Component
{
    public int $cartCount = 0;

    protected $listeners = ['updateCartCount'];

    public function mount(): void
    {
        if (auth()->check())
            if ($order = getOrderCart())
                $this->cartCount = $order->jerseys()->count();
    }

    public function updateCartCount(int $number): void
    {
        if ($number > 0)
            $this->cartCount = $number;
    }

    public function render()
    {
        return view('livewire.navbar-auth');
    }
}

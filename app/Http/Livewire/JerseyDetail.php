<?php

namespace App\Http\Livewire;

use App\Models\Jersey;
use Livewire\Component;

class JerseyDetail extends Component
{
    public string $title;

    public function mount(Jersey $jersey): void
    {
        $this->title = $jersey->name;
    }

    public function render()
    {
        return view('livewire.jersey-detail');
    }
}

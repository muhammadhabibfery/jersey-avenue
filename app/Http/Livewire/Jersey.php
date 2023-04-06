<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\Jersey as JerseyModel;
use App\Models\League as LeagueModel;

class Jersey extends Component
{
    public string $title;

    public function mount(Request $request): void
    {
        $jersey = LeagueModel::where('slug', $request->query('slug'))
            ->first();

        if ($jersey)
            $this->title = $jersey->name;
    }

    public function render()
    {
        return view('livewire.jersey');
    }
}

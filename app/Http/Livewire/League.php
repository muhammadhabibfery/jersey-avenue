<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\League as LeagueModel;

class League extends Component
{
    use WithPagination;

    public string $name = '';

    public static int $paginationCount = 1;

    public function updatingName()
    {
        $this->resetPage();
    }

    public function render()
    {
        $leagues = LeagueModel::where('name', 'LIKE', "%{$this->name}%")
            ->paginate(self::$paginationCount);

        return view('livewire.league', ['leagues' => $leagues]);
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Jersey;
use App\Models\League;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Home extends Component
{
    public Collection $topLeagues, $topJerseys;

    public function mount(): void
    {
        $this->topLeagues = $this->getTopLeagues();
        $this->topJerseys = $this->getTopJerseys(Jersey::$topCount);
    }

    public function render()
    {
        return view('livewire.home');
    }

    private function getTopLeagues(): Collection
    {
        return League::limit(4)
            ->get();
    }

    private function getTopJerseys(int $count): Collection
    {
        return Jersey::bestSeller()
            ->orderBy('sold', 'desc')
            ->limit(4)
            ->get();
    }
}

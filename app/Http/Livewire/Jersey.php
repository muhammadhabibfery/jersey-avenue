<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\Jersey as JerseyModel;
use App\Models\League as LeagueModel;
use Livewire\WithPagination;

class Jersey extends Component
{
    use WithPagination;

    public ?string $title, $leagueSelected = null, $name = '';

    public array $allLeagues;

    public static int $paginationCount = 12;

    public function mount(Request $request): void
    {
        $this->allLeagues = LeagueModel::all()
            ->pluck('name', 'slug')
            ->toArray();

        $jersey = LeagueModel::where('slug', $request->query('name'))
            ->first();

        if ($jersey) {
            $this->title = $jersey->name;
            $this->leagueSelected = $request->query('name');
        }
    }

    public function updatingName()
    {
        $this->resetPage();
    }

    public function render(Request $request)
    {
        $jerseys = JerseyModel::where('name', 'LIKE', "%{$this->name}%");

        if ($request->query('name') || $this->leagueSelected)
            $jerseys->hasLeague($this->leagueSelected);

        return view('livewire.jersey', ['jerseys' => $jerseys->paginate(self::$paginationCount)]);
    }
}

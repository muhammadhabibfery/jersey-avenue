<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Jersey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class JerseyTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(Jersey::class);

        $component->assertStatus(200);
    }
}

<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\JerseyDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class JerseyDetailTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(JerseyDetail::class);

        $component->assertStatus(200);
    }
}

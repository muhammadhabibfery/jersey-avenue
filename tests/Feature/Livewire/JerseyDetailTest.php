<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Jersey;
use Livewire\Livewire;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use App\Http\Livewire\JerseyDetail;
use Livewire\Testing\TestableLivewire;

class JerseyDetailTest extends TestCase
{
    private Jersey $jersey;
    private array $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
        $this->jersey = Jersey::bestSeller()
            ->inRandomOrder()
            ->first();

        $this->data = [
            'size' => Jersey::$sizes[1],
            'quantity' => 1,
            'nameset' => [
                ['name' => null, 'number' => null]
            ]
        ];
    }

    /** @test */
    public function jersey_detail_page_can_be_rendered(): void
    {
        $res = $this->get(route('jersey.detail', $this->jersey));

        $res->assertSee(trans(':club jersey details', ['club' => $this->jersey->name]))
            ->assertSeeInOrder($this->jersey->stock);
    }

    /** @test */
    public function jersey_detail_page_contains_livewire_component(): void
    {
        $res = $this->get(route('jersey.detail', $this->jersey));

        $res->assertSeeLivewire(JerseyDetail::class);
    }

    /** @test */
    public function jersey_detail_page_can_fill_the_form(): void
    {
        $this->getLivewireTest()
            ->assertHasNoErrors();
    }

    /** @test */
    public function jersey_detail_page_size_validation_should_be_dispatched(): void
    {
        $this->data['size'] = 'XXL';

        $this->getLivewireTest($this->data)
            ->assertHasErrors(['size']);
    }

    /** @test */
    public function jersey_detail_page_quantity_validation_should_be_dispatched(): void
    {
        $this->data['quantity'] = 99;

        $this->getLivewireTest($this->data)
            ->assertHasErrors(['quantity']);
    }

    /** @test */
    public function jersey_detail_page_nameset_validation_should_be_dispatched(): void
    {
        $this->data['nameset'] = [['name' => 'john lennon', 'number' => null]];

        $this->getLivewireTest($this->data)
            ->assertHasErrors(['nameset.0.number']);
    }

    /** @test */
    public function not_authenticated_user_should_be_redirect_to_login_page_after_fill_the_form(): void
    {
        $this->getLivewireTest()
            ->assertHasNoErrors()
            ->assertRedirect(route('login'))
            ->assertSessionMissing('status');
    }

    /** @test */
    public function authenticated_user_should_be_redirect_to_cart_page_after_fill_the_form(): void
    {
        $user = $this->authenticatedUser(['role' => User::$roles[2]]);
        $this->data['nameset'] = [
            ['name' => 'johnlennon', 'number' => 12],
            ['name' => null, 'number' => null]
        ];

        $this->getLivewireTest()
            ->assertHasNoErrors()
            ->assertEmitted('updateCartCount', 1)
            ->assertSessionHas('status');
    }

    private function getLivewireTest(array $newData = []): TestableLivewire
    {
        $data = count($newData) > 0 ? $newData : $this->data;

        return Livewire::test(JerseyDetail::class, [$this->jersey])
            ->set('size', $data['size'])
            ->set('quantity', $data['quantity'])
            ->set('nameset', $data['nameset'])
            ->call('addToCart');
    }
}

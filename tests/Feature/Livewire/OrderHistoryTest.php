<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Jersey;
use Livewire\Livewire;
use Illuminate\Support\Arr;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use App\Http\Livewire\OrderHistory;
use Illuminate\Database\Eloquent\Collection;

class OrderHistoryTest extends TestCase
{
    private User $user;
    private Collection $orders;
    private Collection $jerseys;
    private array $status;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);

        $this->user = $this->authenticatedUser(['role' => User::$roles[2]]);

        $this->jerseys = Jersey::bestSeller()
            ->inRandomOrder()
            ->take(2)
            ->get();
        $total = $this->jerseys[0]->price;
        $total += $this->jerseys[1]->price;

        $this->status = Arr::except(Order::$status, 0);
        $this->orders = Order::factory(['user_id' => $this->user->id, 'total_price' => $total, 'status' => Arr::random($this->status, 1)[0]])
            ->count(20)
            ->create();

        foreach ($this->orders as $order) {
            $createPivotTable = $order->jerseys();

            foreach ($this->jerseys as $jersey) {
                $createPivotTable->attach(
                    $jersey->id,
                    ['size' => 'M', 'quantity' => 1, 'total_price' => $jersey->price]
                );
            }
        }
    }

    /** @test */
    public function order_page_contains_livewire_component(): void
    {
        $this->get(route('order-history'))
            ->assertSeeLivewire(OrderHistory::class);
    }

    /** @test */
    public function order_page_with_pagination_can_be_rendered(): void
    {
        $res = $this->get(route('order-history'));

        $res->assertSee(trans('Order History'))
            ->assertSeeInOrder($this->status)
            ->assertSeeInOrder($this->getPaginationData());
    }

    /** @test */
    public function order_page_with_pagination_page_2_can_be_rendered(): void
    {
        $res = $this->get(route('order-history', ['page' => 2]));

        $res->assertSee(trans('Order History'))
            ->assertSeeInOrder($this->status)
            ->assertSeeInOrder($this->getPaginationData(true));
    }

    /** @test */
    public function order_page_can_search_order_by_status_option(): void
    {
        $status = Arr::random($this->status)[0];

        $res = Livewire::test(OrderHistory::class)
            ->set('selectedStatus', $status);

        $res->assertSeeInOrder($this->getPaginationData(status: $status));
    }

    /** @test */
    public function the_modal_detail_order_with_jerseys_related_should_showing_when_user_click_the_detail_button(): void
    {
        $order = $this->orders->random(1)
            ->first();
        $jerseyNames = $order->jerseys
            ->pluck('name')
            ->toArray();

        $res = Livewire::test(OrderHistory::class)
            ->call('orderDetail', true, $order->id);

        $res->assertSee(trans('Order details'))
            ->assertSeeInOrder($jerseyNames);
    }

    /** @test */
    public function the_modal_detail_order_with_jerseys_related_should_hidden_when_user_click_the_close_button(): void
    {
        $order = $this->orders->random(1)
            ->first();
        $jerseyNames = $order->jerseys
            ->pluck('name')
            ->toArray();

        $res = Livewire::test(OrderHistory::class)
            ->call('orderDetail');

        $res->assertSee(trans('Order details'))
            ->assertDontSee($jerseyNames);
    }

    private function getPaginationData(bool $secondPage = false, ?string $status = null): array
    {
        $orders = $this->user
            ->orders()
            ->take(OrderHistory::$paginationCount);

        if ($status)
            $orders->where('status', $status);

        if ($secondPage)
            $orders->skip(OrderHistory::$paginationCount);

        return $orders->get()
            ->pluck('invoice_number')
            ->toArray();
    }
}

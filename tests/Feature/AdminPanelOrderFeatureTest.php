<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Jersey;
use Illuminate\Support\Arr;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;

class AdminPanelOrderFeatureTest extends TestCase
{
    private User $user;
    private Collection $orders;
    private Order $order;
    private Collection $jerseys;
    private int $paginationCount = 10;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);

        $this->user = $this->authenticatedUser(['role' => User::$roles[0]]);

        $total = 0;
        $this->jerseys = Jersey::bestSeller()
            ->inRandomOrder()
            ->take(12)
            ->get();
        foreach ($this->jerseys as $jersey)
            $total += $jersey->price;

        $this->orders = Order::factory(['user_id' => $this->user->id, 'total_price' => $total, 'status' => Arr::random(Order::$status, 1)[0]])
            ->count(19)
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
        $this->order = $this->orders
            ->random(1)
            ->first();
    }

    /** @test */
    public function order_menu_list_can_be_rendered(): void
    {
        $this->get(OrderResource::getUrl())
            ->assertSuccessful()
            ->assertSee(trans('List of orders'));
    }

    /** @test */
    public function order_menu_list_can_show_table_records(): void
    {
        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData());
    }

    /** @test */
    public function order_menu_list_pagination_page_2_can_be_rendered(): void
    {
        Livewire::withQueryParams(['page' => 2])
            ->test(ListOrders::class)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData(true));
    }

    /** @test */
    public function order_menu_list_can_search_order_by_invoice_number(): void
    {
        $invoiceNumber = $this->order
            ->invoice_number;

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData())
            ->searchTable($invoiceNumber)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData(invoiceNumber: $invoiceNumber));
    }

    /** @test */
    public function order_menu_list_can_filter_order_by_status(): void
    {
        $status = Arr::random(Order::$status);

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData())
            ->filterTable('status', $status)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData(status: $status));
    }

    /** @test */
    public function order_menu_list_can_search_order_by_invoice_number_and_filter_by_status(): void
    {
        $invoiceNumber = $this->order
            ->invoice_number;
        $status = Arr::random(Order::$status);

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData())
            ->searchTable($invoiceNumber)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData(invoiceNumber: $invoiceNumber))
            ->filterTable('status', $status)
            ->assertCanSeeTableRecords($this->getOrdersPaginationData(invoiceNumber: $invoiceNumber, status: $status));
    }

    /** @test */
    public function order_menu_view_can_be_rendered(): void
    {
        $this->get(OrderResource::getUrl('view', ['record' => $this->order->invoice_number]))
            ->assertSuccessful()
            ->assertSee(trans('Detail of orders'));
    }

    /** @test */
    public function order_menu_view_can_show_table_records(): void
    {
        Livewire::withQueryParams(['record' => $this->order->invoice_number])
            ->test(ViewOrder::class)
            ->assertSeeInOrder($this->getJerseysPaginationData($this->order->invoice_number));
    }

    /** @test */
    public function order_menu_view_pagination_page_2_can_be_rendered(): void
    {
        Livewire::withQueryParams(['record' => $this->order->invoice_number, 'page' => 2])
            ->test(ViewOrder::class)
            ->assertSeeInOrder($this->getJerseysPaginationData($this->order->invoice_number, true));
    }

    private function getOrdersPaginationData(bool $secondPage = false, ?string $invoiceNumber = null, ?string $status = null): ?Collection
    {
        $orders = $this->user
            ->orders()
            ->where('invoice_number', 'LIKE', "%$invoiceNumber%")
            ->take($this->paginationCount);

        if ($status)
            $orders->where('status', $status);

        if ($secondPage)
            $orders->skip($this->paginationCount);

        return $orders->get();
    }

    private function getJerseysPaginationData(string $invoiceNumber, bool $secondPage = false): array
    {
        $jerseys = $this->user
            ->orders()
            ->where('invoice_number', $invoiceNumber)
            ->first()
            ->jerseys()
            ->take($this->paginationCount);

        if ($secondPage)
            $jerseys->skip($this->paginationCount);

        return $jerseys->get()
            ->pluck('name')
            ->toArray();
    }
}

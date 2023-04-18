<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderHistory extends Component
{
    use WithPagination;

    public ?string $selectedStatus = null;
    public array $status;
    public bool $modalButton = false;
    public ?Collection $jerseys;
    public static int $paginationCount = 15;

    public function mount(): void
    {
        $this->status = Order::$status;
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function orderDetail(bool $isActive = false, ?int $orderId = null): void
    {
        $this->modalButton = $isActive;

        if ($this->modalButton)
            $this->jerseys = $this->getJerseysByOrderId($orderId);
    }

    public function render()
    {
        $orders = $this->getOrders();
        return view('livewire.order-history', ['orders' => $orders]);
    }

    private function getOrders(): LengthAwarePaginator
    {
        $orders = auth()->user()
            ->orders()
            ->where('status', '!=', Order::$status[0]);

        if ($this->selectedStatus)
            $orders->where('status', $this->selectedStatus);

        return $orders->paginate(self::$paginationCount);
    }

    private function getJerseysByOrderId(int $orderId): ?Collection
    {
        return auth()->user()
            ->orders()
            ->where('id', $orderId)
            ->first()
            ->jerseys;
    }
}

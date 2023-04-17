<?php

namespace App\Http\Livewire;

use Midtrans\Snap;
use App\Models\City;
use App\Models\User;
use App\Models\Order;
use App\Models\Jersey;
use Livewire\Component;
use App\Models\Province;
use App\Services\Facades\Shipping;
use Livewire\Redirector;
use Illuminate\Support\Arr;
use App\Traits\MidtransPayment;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class Cart extends Component
{
    use MidtransPayment;

    public ?Order $order;
    public ?Collection $provinces, $cities;
    public array $couriers, $courierCosts, $validationAttributes = [];
    public mixed $selectedProvince = null, $selectedCity = null, $origin = 40;
    public ?string $phone, $address, $selectedCourier, $selectedCourierCost = null;


    public function mount(): void
    {
        $this->order = getOrderCart();
        $this->phone = auth()->user()->phone;
        $this->provinces = Province::all();
        $this->couriers = Shipping::couriers();
        $this->validationAttributes = $this->fieldName();
        $this->checkJerseyStock();
    }

    protected function rules(): array
    {
        foreach ($this->couriers as $courier)
            $couriers[] = $courier['id'];

        return [
            'phone' => ['required', 'numeric', 'digits_between:11,13', Rule::unique(User::class, 'phone')->ignore(auth()->user())],
            'address' => ['required', 'string'],
            'selectedProvince' => ['required', 'numeric', Rule::exists(Province::class, 'id')],
            'selectedCity' => ['required', 'numeric', Rule::exists(City::class, 'id')],
            'selectedCourier' => ['required', Rule::in($couriers)],
        ];
    }

    public function updated(string $propertyName, mixed $value): void
    {
        if ($propertyName === 'selectedProvince') {
            $this->cities = City::where('province_id', $value)
                ->get();
            if ($this->selectedCity)
                $this->selectedCity = null;
        }

        $this->validateOnly($propertyName);
    }

    public function updatedSelectedCourier(string $value): void
    {
        if ($this->selectedCourierCost)
            $this->selectedCourierCost = null;
        $this->courierCosts = [];

        $this->validate();
        $availableCourierServiceCosts = Shipping::cost($this->origin, $this->selectedCity, $this->getTotalWeight(), $this->selectedCourier)->get();

        $this->courierCosts = $this->getCourierServicesCosts($availableCourierServiceCosts, $value);
    }

    public function updatedSelectedCourierCost(string $value): void
    {
        $this->validateOnly('selectedCourierCost', ['selectedCourierCost' => ['required_with:selectedCourier']]);
        $this->validateCourierServiceCosts($value);
    }

    public function removeJersey(array $pivot): Redirector
    {

        if (!$this->order->jerseys()->find($pivot['jersey_id']))
            return to_route('cart');

        $removeJersey = $this->order->jerseys()
            ->wherePivot('id', $pivot['id'])
            ->detach($pivot['jersey_id']);

        if ($removeJersey) {
            $this->order->total_price -= $pivot['total_price'];
            $this->order->save();
        }

        if ($this->order->jerseys()->count() < 1)
            $this->order->delete();

        $this->emit('updateCartCount', $this->order->jerseys()->count());

        return to_route('cart')->with('status', trans('Jersey deleted successfully'));
    }

    public function checkout(): Redirector
    {
        $cost = $this->validateCourierServiceCosts($this->selectedCourierCost);

        auth()->user()
            ->update(['city_id' => $this->selectedCity, 'phone' => $this->phone]);
        $this->order
            ->update(['total_price' => $this->order->total_price += $cost['value'], 'courier_services' => $cost]);

        return $this->sendPaymentCredentials($this->order, $cost, app(Snap::class));
    }

    public function render(): View
    {
        return view('livewire.cart');
    }

    private function checkJerseyStock(): void
    {
        if ($this->order) {
            $jerseysEmptyStockName = ['cart' => []];
            $totalEmptyStock = 0;

            foreach ($this->order->jerseys as $jersey) {
                if ($jersey->stock[$jersey->pivot->size] < $jersey->pivot->quantity) {
                    array_push($jerseysEmptyStockName['cart'], "{$jersey->name} size {$jersey->pivot->size}");
                    $totalEmptyStock++;

                    $this->updateOrder($jersey);
                    $this->order->jerseys()->detach($jersey->id);
                }
            }

            if ($totalEmptyStock > 0) {
                $jerseysEmptyStockName = array_merge($jerseysEmptyStockName, ['title' => trans("We're sorry there is one or several jerseys of your choice that are empty :")]);
                session()->flash('status', $jerseysEmptyStockName);
            }
        }
    }

    private function updateOrder(Jersey $jersey): void
    {
        if ($this->order->jerseys()->count() < 2)
            $this->order->delete();
        else
            $this->order->update(['total_price' => $this->order->total_price - $jersey->price * $jersey->pivot->quantity]);
    }

    private function getCourierServicesCosts(array $availableCosts, string $value): array
    {
        if (count($availableCosts[0]['costs']) < 1)
            throw ValidationException::withMessages(
                ['selectedCourier' => trans('The selected :attribute :value cost is not available, please choose another', ['attribute' => trans('Courier Services'), 'value' => strtoupper($value)])]
            );

        return $this->setCosts($availableCosts);
    }

    private function setCosts(array $availableCosts): array
    {
        $result = ['code' => $availableCosts[0]['code']];
        $courierCosts = [];

        foreach ($availableCosts[0]['costs'] as $costs) {
            unset($costs['cost'][0]['note']);

            $value = $costs['cost'][0]['value'];
            $etd = head(explode(' ', $costs['cost'][0]['etd'])) . ' Hari';
            $costs = array_merge($costs, ['value' => $value, 'etd' => $etd]);

            unset($costs['cost']);
            array_push($courierCosts, $costs);
        }

        $result = array_merge($result, ['costs' => $courierCosts]);
        return $result;
    }

    private function validateCourierServiceCosts(string $value): array
    {
        $value = explode(',', $value);
        $keys = ['code', 'service', 'value'];
        $result = array_combine($keys, $value);
        $availableCost = ['code' => $this->courierCosts['code']];

        foreach ($this->courierCosts['costs'] as $cost) {
            if ($cost['service'] == $result['service']) {
                $availableCost = array_merge($availableCost, Arr::only($cost, ['service', 'value']));
                $value = array_merge($availableCost, $cost);
                break;
            }
        }

        if (!count(array_intersect($availableCost, $result)) == 3)
            throw ValidationException::withMessages([
                'selectedCourierCost' => trans('validation.in', ['attribute' => trans('Courier services cost')])
            ]);

        return $value;
    }

    private function fieldName(): array
    {
        return [
            'phone' => trans('Phone'),
            'address' => trans('Address'),
            'selectedProvince' => trans('Province'),
            'selectedCity' => trans('City'),
            'selectedCourier' => trans('Courier Services'),
            'selectedCourierCost' => trans('Courier services cost')
        ];
    }

    private function getTotalWeight(): int
    {
        $totalWeight = 0;
        foreach ($this->order->jerseys as $jersey)
            $totalWeight += $jersey->weight * $jersey->pivot->quantity;

        return $totalWeight;
    }
}

<?php

namespace App\Http\Livewire;

use Closure;
use App\Models\Order;
use App\Models\Jersey;
use Livewire\Component;
use Livewire\Redirector;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Translation\PotentiallyTranslatedString;

class JerseyDetail extends Component
{
    public ?string $title, $size = null;
    public mixed $quantity = null;
    public Jersey $jersey;
    public array $nameset = [
        ['name' => null, 'number' => null]
    ];

    protected array $validationAttributes;

    public function mount(Jersey $jersey): void
    {
        $this->title = $jersey->name;
        $this->jersey = $jersey;
        if (auth()->check() && session()->has('cartData')) {
            if (session('cartData')['ip'] === request()->ip()) {
                $this->size = session('cartData')['size'];
                $this->quantity = session('cartData')['quantity'];
                $this->nameset = session('cartData')['nameset'];
            }
            session()->forget('cartData');
        }
    }

    protected function rules(): array
    {
        $this->validationAttributes = ['quantity' => trans('Quantity')];
        return [
            'quantity' => [
                'numeric',
                'gte:1',
                function (string $attribute, string $value, Closure $fail): ?PotentiallyTranslatedString {
                    if (isset($this->size)) {
                        foreach ($this->jersey->stock as $key => $stock) {
                            if ($this->size === $key) {
                                if ($value > $stock)
                                    return  $fail(trans("The Stock is not enought"));
                            }
                        }
                    }
                    return null;
                }
            ],
            'size' => ['string', Rule::in(Jersey::$sizes)]
        ];
    }

    public function updated(string $propertyName): void
    {
        if ($propertyName === 'quantity' || $propertyName === 'size') {
            $this->$propertyName = ltrim($this->$propertyName);
            if (!$this->$propertyName) {
                throw ValidationException::withMessages([
                    $propertyName => trans("The :name field cannot be empty or contain only spaces or zero number", ['name' => $propertyName])
                ]);
                $this->$propertyName = null;
            }
        }
        $this->validateOnly($propertyName);
    }

    public function addMoreNameset(): void
    {
        $this->validateNameset();
        $this->nameset[] = ['name' => null, 'number' => null];
    }

    public function removeNameset(int $key): void
    {
        if (count($this->nameset) < 2) {
            $this->nameset[0]['name'] = null;
            $this->nameset[0]['number'] = null;
        } else {
            $this->reIndexNameset($key);
        }
    }

    public function addToCart(): Redirector
    {
        $this->validateNameset(true);
        $this->trimNameset();
        [$totalPrice, $sessionData, $nameset] = $this->setData();

        if (!auth()->check()) {
            session()->put('cartData', $sessionData);
            session()->put('cartRoute', route('jersey.detail', $this->jersey));
            return to_route('login');
        }

        if (is_null($order = getOrderCart()))
            $order = $this->createOrder($totalPrice);
        else
            $order->update(['total_price' => $order->total_price += $totalPrice]);

        $this->emit('updateCartCount', $this->addJerseyOrder($order, $totalPrice, $nameset));

        $status = [
            'title' => trans('The orders has been added to your cart'),
            'icon' => '🛒',
            'paragraph' => trans('Please check your cart to checkout your order')
        ];

        return to_route('jersey.detail', $this->jersey)
            ->with('status', $status);
    }

    public function render()
    {
        return view('livewire.jersey-detail');
    }

    private function validateNameset(bool $addToCart = false): void
    {
        $this->validate(
            [
                'nameset' => 'array',
                'nameset.*.name' => ['required_with:nameset.*.number', 'nullable', 'string', 'max:25'],
                'nameset.*.number' => ['required_with:nameset.*.name', 'nullable', 'lte:99', 'gte:1', 'numeric']
            ],
            attributes: [
                'nameset.*.name' => trans('Name'),
                'nameset.*.number' => trans('Number')
            ]
        );

        foreach ($this->nameset as $key => $ns) {
            $this->nameset[$key]['name'] = ltrim($this->nameset[$key]['name']);
            $this->nameset[$key]['number'] = ltrim($this->nameset[$key]['number']);

            if (empty($this->nameset[$key]['name']) && empty($this->nameset[$key]['number'])) {
                if (!$addToCart)
                    throw ValidationException::withMessages([
                        "addMoreNameset" => trans('If you want to add more nameset, The Name and Number column must be filled')
                    ]);
                else
                    $this->reIndexNameset($key, $ns);
            }
        }

        $this->validate();
    }

    private function reIndexNameset(int $key): void
    {
        unset($this->nameset[$key]);
        $this->nameset = array_values($this->nameset);
    }

    private function trimNameset(): void
    {
        $newNameset = [];
        foreach ($this->nameset as $key1 => $ns)
            $newNameset[$key1] = ['name' => ltrim(ucwords($ns['name'])), 'number' => ltrim($ns['number'])];

        $this->nameset = $newNameset;
    }

    private function setData(): array
    {
        $totalPrice = $this->jersey->price * $this->quantity;
        $totalNamesetPrice = 0;
        $data = ['ip' => request()->ip(), 'size' => $this->size, 'quantity' => $this->quantity];

        if (count($this->nameset) > 0) {
            $totalNamesetPrice += $this->jersey->price_nameset * count($this->nameset);
            $data = array_merge($data, ['nameset' => $this->nameset]);
        } else {
            $data = array_merge($data, ['nameset' => [['name' => null, 'number' => null]]]);
        }

        $totalPrice += $totalNamesetPrice;

        return [$totalPrice, $data, $this->nameset];
    }

    private function createOrder(int $totalPrice): Order
    {
        $data = [
            'invoice_number' => generateInvoiceNumber(),
            'total_price' => $totalPrice,
            'status' => Order::$status[0]
        ];

        return auth()->user()
            ->orders()
            ->create($data);
    }

    private function addJerseyOrder(Order $order, int $totalPrice, array $nameset): int
    {
        $data = ['size' => $this->size, 'quantity' => $this->quantity, 'total_price' => $totalPrice];

        if (count($nameset) > 0)
            $data = array_merge($data, ['nameset' => json_encode($nameset)]);

        $order->jerseys()
            ->attach($this->jersey->id, $data);

        return $order->jerseys()
            ->count();
    }
}

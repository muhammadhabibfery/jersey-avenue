<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Jersey;
use Livewire\Livewire;
use App\Http\Livewire\Cart;
use App\Mail\OrderSuccess;
use App\Services\RajaOngkir;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\DB;
use Livewire\Testing\TestableLivewire;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Midtrans\Snap;
use Mockery\MockInterface;
use stdClass;

class CartTest extends TestCase
{
    private User $user;
    private Order $order;
    private Collection $jerseys;
    private array $data;
    private string $invoiceNumber,
        $selectedCourierCost = 'jne,REG,41000',
        $token = 'randomToken',
        $urlPayment = 'https://app.sandbox.midtrans.com/snap/v3/redirection/randomString';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
        $this->createProvinces();
        $this->createCities();

        $this->user = $this->authenticatedUser(['role' => User::$roles[2]]);

        $this->jerseys = Jersey::bestSeller()
            ->inRandomOrder()
            ->take(2)
            ->get();
        $total = $this->jerseys[0]->price;
        $total += $this->jerseys[1]->price;
        $this->order = Order::factory(['user_id' => $this->user->id, 'total_price' => $total, 'status' => Order::$status[0]])
            ->create();

        $createPivotTable = $this->order->jerseys();
        foreach ($this->jerseys as $jersey) {
            $createPivotTable->attach(
                $jersey->id,
                ['size' => 'M', 'quantity' => 1, 'total_price' => $jersey->price]
            );
        }

        $this->invoiceNumber = $this->order->invoice_number;
        $this->data = [
            'phone' => $this->user->phone ?: '12121212121',
            'selectedProvince' => 6,
            'selectedCity' => 42,
            'selectedCourier' => 'jne',
            'address' => 'jl.in aja',
            // 'courierCosts' => [
            //     'code' => 'jne',
            //     'costs' => [
            //         [
            //             "service" => "OKE",
            //             "description" => "Ongkos Kirim Ekonomis",
            //             "value" => 38000,
            //             "etd" => "3-6 Hari",
            //         ],
            //         [
            //             "service" => "REG",
            //             "description" => "Layanan Reguler",
            //             "value" => 41000,
            //             "etd" => "2-3 Hari",
            //         ]
            //     ]
            // ],
            'selectedCourierCost' => 'jne,REG,41000'
        ];
    }

    /** @test */
    public function cart_page_can_be_rendered(): void
    {
        $res = $this->get(route('cart'));

        $res->assertSee(trans('Shopping Cart'))
            ->assertSee($this->jerseys->count())
            ->assertSeeInOrder($this->jerseys->pluck('name', 'id')->toArray());
    }

    /** @test */
    public function cart_page_contains_livewire_component(): void
    {
        $this->get(route('cart'))
            ->assertSeeLivewire(Cart::class);
    }

    /** @test */
    public function cart_page_can_remove_selected_jersey(): void
    {
        $jersey = $this->order
            ->jerseys()
            ->first();

        $res = Livewire::test(Cart::class)
            ->call('removeJersey', $jersey->pivot->toArray());

        $res->assertRedirect(route('cart'))
            ->assertSessionHas('status');
        $this->assertTrue($this->order->jerseys()->count() === 1);
        $this->assertDatabaseHas(Order::class, ['invoice_number' => $this->invoiceNumber, 'total_price' => $this->order->total_price / 2]);
    }

    /** @test */
    public function
    cart_page_can_fill_the_form(): void
    {
        $this->createMockShippingService();

        $this->getLivewireTest()
            ->assertSet(array_keys($this->data, $this->data['phone'])[0], $this->data['phone'])
            ->assertSet(array_keys($this->data, $this->data['selectedProvince'])[0], $this->data['selectedProvince'])
            ->assertSet(array_keys($this->data, $this->data['selectedCity'])[0], $this->data['selectedCity'])
            ->assertSet(array_keys($this->data, $this->data['address'])[0], $this->data['address'])
            ->assertSet(array_keys($this->data, $this->data['selectedCourier'])[0], $this->data['selectedCourier'])
            ->assertSet(array_keys($this->data, $this->data['selectedCourierCost'])[0], $this->data['selectedCourierCost']);
    }

    /** @test */
    public function cart_page_validation_should_be_dispatched(): void
    {
        $this->withExceptionHandling();

        $data = ['phone' => null, 'selectedProvince' => 999, 'selectedCity' => 999, 'address' => null, 'selectedCourier' => 'xxx'];

        $this->getLivewireTest($data)
            ->assertHasErrors(['phone', 'selectedProvince', 'selectedCity', 'address', 'selectedCourier']);
    }

    /** @test */
    public function cart_page_can_go_to_the_payment_page_after_successful_checkout(): void
    {

        $cost = (int) last(explode(',', $this->selectedCourierCost));
        $total = [
            currencyFormat($this->order->total_price),
            currencyFormat($cost),
            currencyFormat($this->order->total_price + $cost)
        ];
        $this->createMockShippingService();
        $this->createMockMidtransSnapService();

        $this->getLivewireTest()
            ->call('checkout')
            ->assertHasNoErrors()
            ->assertSeeInOrder($total)
            ->assertRedirect($this->urlPayment);

        $selectedCourierCost = explode(',', $this->data['selectedCourierCost']);
        $this->assertDatabaseHas(Order::class, ['invoice_number' => $this->invoiceNumber, 'total_price' => $this->order->total_price + (int) last($selectedCourierCost)])
            ->assertDatabaseMissing(Order::class, ['invoice_number' => $this->invoiceNumber, 'courier_services' => null]);
    }

    /** @test */
    public function the_order_status_is_pending_when_user_have_not_finished_the_payment_instruction(): void
    {
        $res = $this->get(route(
            'checkout.payment.finish',
            [
                'order_id' => $this->invoiceNumber,
                'status_code' => '201',
                'transaction_status' => 'pending'
            ]
        ));

        $res->assertRedirect(route('checkout.pending'));
        $this->assertDatabaseHas(Order::class, ['invoice_number' => $this->invoiceNumber, 'status' => Order::$status[1]]);
    }

    /** @test */
    public function get_notification_of_failed_order_status_from_midtrans_webhook(): void
    {
        $status = 'cancel';
        $this->createMockMidtransNotificationService($status);

        $res = $this->post(route('checkout.payment.notification'), json_decode($this->notificationData($status), true));

        $res->assertRedirect(route('checkout.failed'));
        $this->assertDatabaseHas(Order::class, ['invoice_number' => $this->invoiceNumber, 'status' => Order::$status[3]]);
    }

    /** @test */
    public function get_notification_of_success_order_status_from_midtrans_webhook(): void
    {
        Mail::fake();
        $status = 'settlement';
        $this->createMockMidtransNotificationService($status);

        $res = $this->post(route('checkout.payment.notification'), json_decode($this->notificationData($status), true));

        Mail::assertQueued(OrderSuccess::class);
        $res->assertRedirect(route('checkout.success'));
        $this->assertDatabaseHas(Order::class, ['invoice_number' => $this->invoiceNumber, 'status' => Order::$status[2]])
            ->assertDatabaseHas(
                Jersey::class,
                ['id' => $this->order->jerseys()->first()->id, 'stock' => json_encode($this->order->fresh()->jerseys->first()->stock)]
            )
            ->assertDatabaseHas(
                Jersey::class,
                ['id' => $this->order->jerseys->last()->id, 'stock' => json_encode($this->order->fresh()->jerseys->last()->stock)]
            );
    }

    private function getLivewireTest(array $newData = []): TestableLivewire
    {
        $data = count($newData) > 0 ? $newData : $this->data;

        $test = Livewire::test(Cart::class)
            ->set('phone', $data['phone'])
            ->set('selectedProvince', $data['selectedProvince'])
            ->set('selectedCity', $data['selectedCity'])
            ->set('address', $data['address'])
            ->set('selectedCourier', $data['selectedCourier']);

        return count($data) < 6 ? $test :
            $test->set('selectedCourierCost', $data['selectedCourierCost']);
    }

    private function createMockMidtransNotificationService(string $status): void
    {
        $mock = $this->mock('overload:' . 'Midtrans\Notification', function (MockInterface $mock): void {
            $mock->shouldReceive('__construct')
                ->once()
                ->withAnyArgs();
        });

        $mock->shouldReceive('getResponse')
            ->once()
            ->andReturn(json_decode($this->notificationData($status)));
    }

    private function createMockShippingService(): void
    {
        $responseData = [
            'rajaongkir' => [
                "status" =>  [
                    "code" => 200,
                    "description" => "OK"
                ],
                'results' => [
                    [
                        "code" => "jne",
                        "name" => "Jalur Nugraha Ekakurir (JNE)",
                        "costs" => [
                            [
                                "service" => "OKE",
                                "description" => "Ongkos Kirim Ekonomis",
                                "cost" => [
                                    [
                                        "value" => 38000,
                                        "etd" => "3-6",
                                        "note" => ""
                                    ]
                                ]
                            ],
                            [
                                "service" => "REG",
                                "description" => "Layanan Reguler",
                                "cost" => [
                                    [
                                        "value" => 41000,
                                        "etd" => "2-3",
                                        "note" => ""
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        Http::fake(fn (): FulfilledPromise => Http::response($responseData));
    }

    private function createMockMidtransSnapService(): void
    {
        $this->mock(Snap::class, function (MockInterface $mock): void {
            $mock->shouldReceive('createTransaction')
                ->once()
                ->andReturnUsing(
                    fn (): stdClass => (object) ['token' => $this->token, 'redirect_url' => $this->urlPayment]
                );
        });
    }

    private function notificationData(string $status): string
    {
        $transactionId = bin2hex(random_bytes(25));
        $expireTime = now()->addDay()->format('o-m-d H:i:s');

        return json_encode([
            'transaction_status' => $status,
            '$transaction_id' => $transactionId,
            'order_id' => $this->invoiceNumber,
            'expire_time' => $expireTime,
            'payment_type' => 'bank_transfer'
        ]);
    }

    private function createProvinces(): bool
    {
        return DB::table('provinces')
            ->insert([
                ['id' => 6, 'name' => 'DKI Jakarta'],
                ['id' => 9, 'name' => 'Jawa Barat']
            ]);
    }

    private function createCities(): bool
    {
        return DB::table('cities')
            ->insert([
                ['id' => 42, 'province_id' => 6, 'name' => 'Jakarta Selatan', 'type' => 'Kota', 'postal_code' => '12345'],
                ['id' => 44, 'province_id' => 6, 'name' => 'Jakarta Utara', 'type' => 'Kota', 'postal_code' => '12345'],
                ['id' => 64, 'province_id' => 9, 'name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '54321'],
                ['id' => 73, 'province_id' => 9, 'name' => 'Cimahi', 'type' => 'Kota', 'postal_code' => '54321'],
            ]);
    }
}

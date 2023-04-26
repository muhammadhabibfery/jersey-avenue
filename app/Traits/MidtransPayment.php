<?php

namespace App\Traits;

use Midtrans\Snap;
use ErrorException;
use Midtrans\Config;
use App\Models\Order;
use Livewire\Redirector;
use App\Mail\OrderSuccess;
use App\Notifications\OrderNotification;
use Midtrans\Notification;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Symfony\Component\HttpFoundation\Response;

trait MidtransPayment
{
    /**
     * the name of available payments.
     */
    public array $availablePayments = ['bri_va', 'bni_va', 'gopay', 'shopeepay'];

    /**
     * set midtrans configuration.
     */
    private function configuration(): void
    {
        Config::$serverKey = config('midtrans.midtrans_serverkey');
        Config::$isProduction = config('midtrans.midtrans_production');
        Config::$isSanitized = config('midtrans.midtrans_sanitized');
        Config::$is3ds = config('midtrans.midtrans_3ds');
    }

    /**
     * set data for payment credentials.
     */
    private function setDataPayment(Order $order): array
    {
        return [
            'transaction_details' => ['order_id' => $order->invoice_number, 'gross_amount' => (int) $order->total_price],
            'customer_details' => ['first_name' => $order->user->name, 'email' => $order->user->email],
            'enabled_payments' => $this->availablePayments,
            'vtweb' => []
        ];
    }

    /**
     * send payment credentials to snap backend (midtrans api).
     */
    public function sendPaymentCredentials(Order $order, array $cost, Snap $snap): Redirector|RedirectResponse
    {
        try {
            $this->configuration();
            return redirect($snap::createTransaction($this->setDataPayment($order))->redirect_url);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * get data from midtrans webhook.
     */
    public function notificationHandler(Request $request): RedirectResponse
    {
        if (count($this->setDataFromMidtrans($request)) < 1)
            return to_route('home');

        [$notificationStatus, $order] = $this->setDataFromMidtrans($request);

        if (in_array($order->status, Arr::only(Order::$status, [2, 3])))
            return redirect()->route('home');

        if (!auth()->check())
            auth()->login($order->user);


        if (in_array($notificationStatus, ['deny', 'expire', 'cancel'])) {
            $order->status = 'FAILED';
            $routeName = 'checkout.failed';
        }
        if ($notificationStatus === 'pending') {
            $order->status = 'PENDING';
            $routeName = 'checkout.pending';
        }
        if ($notificationStatus === 'settlement') {
            $order->status = 'SUCCESS';
            $routeName = 'checkout.success';
            updateJerseyStock($order);
            Mail::to($order->user)->send(new OrderSuccess($order));
        }

        $order->save();
        NotificationFacade::send(getEmployees(), new OrderNotification($order));
        return  to_route($routeName);
    }

    /**
     * set data from midtrans webhook.
     */
    private function setDataFromMidtrans(Request $request): array
    {
        if (count($request->all()) < 1)
            return [];

        if ($request->payment_type) {
            try {
                $this->configuration();
                $notification = (new Notification())->getResponse();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            if ($request->transaction_status)
                $notification = $request;
        };

        return [$notification->transaction_status, $this->getOrder($notification->order_id)];
    }

    /**
     * finish redirect url (set via snap setting or midtrans dashboard).
     */
    public function finish(Request $request): RedirectResponse
    {
        return $this->notificationHandler($request);
    }

    /**
     * unfinish redirect url (set via snap setting or midtrans dashboard).
     */
    public function unfinish(Request $request): RedirectResponse
    {
        return $this->notificationHandler($request);
    }

    /**
     * error redirect url (set via snap setting or midtrans dashboard).
     */
    public function error(Request $request): RedirectResponse
    {
        return $this->notificationHandler($request);
    }

    /**
     * Get Customer order by invoice number.
     */
    private function getOrder(string $invoice_number): Order|RedirectResponse
    {
        $order = Order::where('invoice_number', $invoice_number)
            ->first();

        return $order ?: to_route('home');
    }
}

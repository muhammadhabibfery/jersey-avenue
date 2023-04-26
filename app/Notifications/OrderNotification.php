<?php

namespace App\Notifications;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as NotificationFilament;

class OrderNotification extends Notification
{
    use Queueable;

    public Order $order;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 5;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $titleMessage = trans('New Order Notification');
        $bodyMessage = trans('There is a new order with invoice number :invoiceNumber', ['invoiceNumber' => $this->order->invoice_number]);
        $urlAction = OrderResource::getUrl();

        if ($this->order->status != Order::$status[1]) {
            $titleMessage = trans('Changes of Order Status Notifcation');
            $bodyMessage = trans('The status of order with invoice number :invoiceNumber is :status now', ['invoiceNumber' => $this->order->invoice_number, 'status' => $this->order->status]);
            $urlAction = OrderResource::getUrl('view', ['record' => $this->order->invoice_number]);
        }

        return NotificationFilament::make()
            ->title($titleMessage)
            ->body($bodyMessage)
            ->actions([
                Action::make(trans('see'))
                    ->emit('markAsReadOrderNotification', [$bodyMessage])
                    ->url($urlAction)
            ])
            ->getDatabaseMessage();
    }
}

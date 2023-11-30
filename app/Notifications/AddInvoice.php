<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddInvoice extends Notification
{
    use Queueable;
    private $invoice_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($invoice_id)
    {
        $this->invoice_id = $invoice_id;
       
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('اضافة فاتورة جديده')
                    ->line('اضافة فاتورة جديده')
                    ->action('تفاصيل الفاتوره', url('http://127.0.0.1:8000/InvoiceDetails/'.$this->invoice_id))
                    ->line('شكرا لاستخدامك ميجاسوفت لادارة الفواتير');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

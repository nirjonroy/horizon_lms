<?php
namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $order;

    public function __construct($user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('New Course Order Received')
                    ->markdown('emails.new-order');
    }
}

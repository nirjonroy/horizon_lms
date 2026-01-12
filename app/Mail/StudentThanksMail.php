<?php
namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentThanksMail extends Mailable
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
        return $this->subject('Thanks for your purchase 🎉')
                    ->markdown('emails.student-thanks');
    }
}

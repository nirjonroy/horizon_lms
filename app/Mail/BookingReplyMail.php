<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookingData;

    public function __construct($bookingData)
    {
        $this->bookingData = $bookingData;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Booking Confirmation')
                    ->view('emails.BookingReplyMail')
                    ->with(['bookingData' => $this->bookingData])
                    ->when(
                        !empty($this->bookingData['calendar_ics']),
                        fn ($mail) => $mail->attachData(
                            $this->bookingData['calendar_ics'],
                            'consultation.ics',
                            ['mime' => 'text/calendar']
                        )
                    );
    }
}

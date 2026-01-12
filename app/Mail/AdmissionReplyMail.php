<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentData;

    public function __construct($studentData)
    {
        $this->studentData = $studentData;
    }

    public function build()
    {
        $programTitle = $this->studentData['selected_program_name']
            ?? ($this->studentData['course_name'] ?? 'your program');
        $universityName = $this->studentData['selected_university_name']
            ?? config('app.name', 'Horizons Unlimited');
        $subject = "Your application for {$programTitle} | {$universityName}";

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject)
                    ->view('emails.admission_reply')
                    ->with(['studentData' => $this->studentData]);
    }
}

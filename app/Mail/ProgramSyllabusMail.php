<?php

namespace App\Mail;

use App\Models\onlineFee;
use App\Models\User;
use App\Models\whereToStudy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProgramSyllabusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public whereToStudy $studies,
        public onlineFee $program,
        public string $syllabusPath
    ) {
    }

    public function build(): self
    {
        $programTitle = $this->program->program ?: ($this->program->short_name ?: 'Program');
        $subject = 'Your syllabus for ' . $programTitle . ' | ' . $this->studies->name;

        $mail = $this->subject($subject)
            ->view('emails.program_syllabus')
            ->with([
                'userName' => $this->user->name ?: 'Student',
                'programTitle' => $programTitle,
                'degreeName' => optional($this->program->feesCategory)->name ?: 'Degree Program',
                'duration' => $this->program->duration ?: 'Flexible schedule',
                'programType' => $this->program->type ?: 'Online',
                'universityName' => $this->studies->name,
                'shortDescription' => strip_tags((string) ($this->program->short_description ?? '')),
                'applyUrl' => $this->program->link ?: route('apply.now', $this->program->slug ?? null),
                'consultUrl' => route('consultation.step1'),
                'heroImage' => $this->studies->slider1 ? asset($this->studies->slider1) : asset('frontend/assets/images/hero-banner.jpg'),
                'logoUrl' => asset('frontend/assets/images/logo.png'),
                'totalFee' => (float) ($this->program->total_fee ?? 0),
                'yearlyFee' => (float) ($this->program->yearly ?? 0),
                'highlights' => collect([
                    $this->studies->rank,
                    $this->studies->award,
                    $this->studies->global_network,
                    $this->studies->rated,
                ])->map(function ($value) {
                    $decoded = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $cleaned = trim(strip_tags($decoded));
                    return $cleaned !== '' ? Str::limit($cleaned, 140) : null;
                })->filter()->values()->all(),
            ]);

        $attachmentPath = public_path($this->syllabusPath);
        if (file_exists($attachmentPath)) {
            $filename = Str::slug($programTitle) . '-syllabus.pdf';
            $mail->attach($attachmentPath, ['as' => $filename, 'mime' => 'application/pdf']);
        }

        return $mail;
    }
}

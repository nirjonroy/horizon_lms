<?php

namespace App\Mail;

use App\Models\onlineFee;
use App\Models\User;
use App\Models\whereToStudy;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProgramSyllabusAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public whereToStudy $studies,
        public onlineFee $program,
        public string $ipAddress,
        public string $userAgent
    ) {
    }

    public function build(): self
    {
        $programTitle = $this->program->program ?: ($this->program->short_name ?: 'Program');
        $subject = 'Syllabus downloaded: ' . $programTitle . ' | ' . $this->studies->name;
        $programUrl = route('university.program.show', [
            'slug' => $this->studies->slug,
            'program' => $this->program->slug,
        ]);

        return $this->subject($subject)
            ->view('emails.program_syllabus_admin')
            ->with([
                'programTitle' => $programTitle,
                'degreeName' => optional($this->program->feesCategory)->name ?: 'Degree Program',
                'duration' => $this->program->duration ?: 'Flexible schedule',
                'programType' => $this->program->type ?: 'Online',
                'universityName' => $this->studies->name,
                'shortDescription' => strip_tags((string) ($this->program->short_description ?? '')),
                'userName' => $this->user->name ?: 'Student',
                'userEmail' => $this->user->email,
                'userId' => $this->user->id,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'downloadedAt' => now()->format('M d, Y g:i A'),
                'programUrl' => $programUrl,
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
    }
}

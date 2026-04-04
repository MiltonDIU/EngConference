<?php

namespace App\Mail;

use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractAccepted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $paper;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Paper $paper)
    {
        $this->paper = $paper->load(['user.profile', 'track', 'subTrack']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->paper->user->email)
                    ->subject('Abstract Accepted – BNC2026')
                    ->view('mail.abstract_accepted');
    }
}

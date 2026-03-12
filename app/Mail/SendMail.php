<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;
class SendMail extends Mailable
{
    use Queueable, SerializesModels;
protected $user, $message,$data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'user' => $this->user,
            'message' => $this->message,
        ];
        return $this->subject($this->message->subject)
            ->view('mail.sendMail', ['data' => $data]);

    }
}

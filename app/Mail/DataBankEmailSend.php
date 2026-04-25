<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DataBankEmailSend extends Mailable
{
    use Queueable, SerializesModels;
    protected $dataBank, $message,$data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dataBank,$message)
    {
        $this->dataBank = $dataBank;
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
            'dataBank' => $this->dataBank,
            'message' => $this->message,
        ];
        return $this->subject($this->message->subject)
            ->view('mail.data-bank-send-email', ['data' => $data]);
    }
}

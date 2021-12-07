<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * 問い合わせメール送信
 * Class SendMail
 * @package App\Mail
 */
class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $info;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($info)
    {
        //
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.information')
            ->subject("【ナカマーカー】問い合わせ")
            ->from([env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')])
            ->with(['info' => $this->info]);
    }
}

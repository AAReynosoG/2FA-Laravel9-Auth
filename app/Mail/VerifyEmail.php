<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * VerifyEmail Mailable class
 *
 * This class is responsible for sending the email to the user for email verification.
 * It receives the verification URL as a parameter and sends an email with a view
 * that includes the verification link.
 */
class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The verification URL to be sent in the email.
     *
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @param string $url The URL to be included in the verification email.
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * This method sets the subject of the email and loads the email view,
     * passing the URL parameter to the view.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Verify Your Email Address')
        ->view('emails.verify_email')
        ->with(['url' => $this->url]);
    }
}


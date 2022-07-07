<?php

namespace App\Mail;

use Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailToResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = env('CLIENT_RESET_PASSWORD_URL');
        $fromEmail = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
        $url = str_replace('__token__', $this->token->plainTextToken, $url);

        return $this->from($fromEmail, $fromName)
            ->subject('Definição de senha')
            ->markdown('mails.reset-password')
            ->with([
                'name' => $this->user->name,
                'link' => $url
            ]);
    }
}

<?php

namespace Users\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Users\User;

class UserPasswordSettingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(readonly private User $user, readonly private string $token)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $frontUrl = config('app.front_url');
        $url = "$frontUrl/#/password-setting/{$this->token}";

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(__('password_setting'))
            ->markdown('mails.password-setting')
            ->with([
                'name' => $this->user->name,
                'link' => $url
            ]);
    }
}

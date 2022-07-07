<?php

use Users\User;
use Illuminate\Support\Facades\Artisan;

/*
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
*/

Artisan::command('user:block-for-expired-access-time', function () {
    $this->comment("Bloqueio de usuários com tempo de acesso expirado");

    User::query()
        ->whereNotIn('status', [User::STATUS_BLOCKED, User::STATUS_BLOCKED_BY_TIME])
        ->where('expires_in', '<', new \DateTime())
        ->update([
            'status' => User::STATUS_BLOCKED_BY_TIME
        ]);
})->purpose('Bloqueia usuários com tempo de acesso expirado');

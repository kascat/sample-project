<?php

namespace App\Utils;

class Server
{
    public static function getIPAddress(): ?string
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return request()->getClientIp();
    }
}

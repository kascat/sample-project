<?php

namespace App\Utils;

class Formatter
{
    public static function formatBrazilianPhoneNumber(?string $phoneNumber): ?string
    {
        if (null === $phoneNumber) {
            return null;
        }

        if (Str::length($phoneNumber) === 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", '($1) $2-$3', $phoneNumber);
        }

        return preg_replace("/(\d{2})(\d{5})(\d{4})/", '($1) $2-$3', $phoneNumber);
    }

    public static function formatBrazilianCpf(string $cpf): string
    {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', $cpf);
    }

    public static function onlyNumbers($value): array|string|null
    {
        return $value ? preg_replace('/\D/', '', $value) : $value;
    }

    public static function camelToSnakeCase($string): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string ?: ''));
    }
}

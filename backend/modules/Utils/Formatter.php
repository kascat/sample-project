<?php

namespace Utils;

/**
 * Class Formatter
 * @package BS\Utils
 */
class Formatter
{
    /**
     * @param $value
     * @return array|string|string[]|null
     */
    public static function formatCnpjCpf($value)
    {
        if (!$value) {
            return $value;
        }

        $CPF_LENGTH = 11;
        $cnpj_cpf   = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    /**
     * @param $item
     * @return string|null
     */
    public static function formatAddress($item)
    {
        if (!$item) {
            return null;
        }

        return "${item['street']} ${item['number']}, ${item['neighborhood']} - ${item['city']}/${item['state']}";
    }

    /**
     * @param $item
     * @return string|null
     */
    public static function formatSimpleAddress($item)
    {
        if (!$item) {
            return null;
        }

        $city = $item['city'] ?? '';
        $state = $item['state'] ?? '';
        $street = $item['street'] ?? '';
        $number = $item['number'] ?? '';

        $address = $city;
        $address .= $address && $state ? "/${state}" : $state;
        $address .= $address && $street ? " - ${street}" : $street;
        $address .= $address && $number ? ", ${number}" : $number;

        return $address;
    }

    /**
     * @param $date
     * @param $format
     * @return string|null
     * @throws \Exception
     */
    public static function formatDate($date, $format)
    {
        return $date ? (new \DateTime($date))->format($format) : $date;
    }
}

<?php

namespace App\Rules;

use App\Utils\Validators\Cnpj;
use Illuminate\Contracts\Validation\Rule;

class CnpjRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return (new Cnpj())->validate($value);
    }

    public function message(): string
    {
        return 'O campo :attribute deve ser um CNPJ válido.';
    }
}

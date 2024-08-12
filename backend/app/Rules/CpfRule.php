<?php

namespace App\Rules;

use App\Utils\Validators\Cpf;
use Illuminate\Contracts\Validation\Rule;

class CpfRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return (new Cpf())->validate($value);
    }

    public function message(): string
    {
        return 'O campo :attribute deve ser um CPF válido.';
    }
}

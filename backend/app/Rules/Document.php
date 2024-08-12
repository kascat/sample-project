<?php

namespace App\Rules;

use App\Utils\Validators\Cnpj;
use App\Utils\Validators\Cpf;
use Illuminate\Contracts\Validation\Rule;

class Document implements Rule
{
    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $isCPF = (new Cpf())->validate($value);
        $isCNPJ = (new Cnpj())->validate($value);

        return $isCPF || $isCNPJ;
    }

    public function message(): string
    {
        return 'O campo :attribute deve ser um CPF/CNPJ válido.';
    }
}

<?php

namespace Sample;

use App\Http\Requests\Request;

/** */
class SampleRequest extends Request
{
    /** */
    public function validateToTest()
    {
        return [
            'id'    => 'required|',
        ];
    }

    /** */
    public function autorizeToTest()
    {
        return true;
    }

    /** */
    public function messages()
    {
        return [
            'id.required'    => 'O id é obrigatório',
        ];
    }
}

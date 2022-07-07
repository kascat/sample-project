<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Request extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = "validateTo" . Str::ucfirst($this->route()->getActionMethod());

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $method = "authorizeTo" . Str::ucfirst($this->route()->getActionMethod());

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return true;
    }

    /**
     * Configuration for exception handler
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "firstname" => "required",
            "lastname" => "required",
            "email" => "required|string|unique:users,email",
            "password" => "required|string|min:6",
            "phone_number" => "integer|required|unique:users,phone_number",
            "referedBy" => "string",

            // others
            "bio" => "string|nullable",
            "address" => "string|nullable",
            "gender" => "string|nullable",
            "date_of_birth" => "date|nullable",
            "image" => "string|nullable|max:1000",
            // "registration_fee_paid" => "required"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            [
                "success" => false,
                "errors" => $validator->errors(),
                "data" => [],
            ],
            422
        ));
    }
}

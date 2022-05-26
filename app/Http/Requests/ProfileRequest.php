<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
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
            "bio" => "string|nullable",
            "address" => "string|nullable",
            "gender" => "string|nullable",
            "date_of_birth" => "date|nullable",
            "image" => "string|nullable|max:1000",
            "firstname" => "string",
            "lastname" => "string",
            "email" => "email|string",
        ];
    }
}


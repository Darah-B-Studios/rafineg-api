<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectionRequest extends FormRequest
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
            "amount" => 'required',
            "phoneNumber" => 'required',
            "description" => 'required|string',
            "externalReference" => 'string|nullable',
            "collectionType" => 'string|required',
            "collectionTypeCode" => 'string|nullable',
            "referalCode" => 'string|nullable'
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth()->id() ? true : false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"name" => "string|required",
			"image" => "string",
			"description" => "string|required",
			"fee" => "numeric|required",
			"low_investment_limit" => "numeric",
			"high_investment_limit" => "numeric",
		];
	}
}

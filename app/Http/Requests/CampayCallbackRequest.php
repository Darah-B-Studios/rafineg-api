<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampayCallbackRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'status'				=> 'required',
			'reference'				=> 'required',
			'amount'				=> 'required',
			'currency'				=> 'required',
			'operator'				=> 'required',
			'code'					=> 'required',
			'operator_reference'	=> 'required',
			'signature'				=> 'required'
		];
	}
}

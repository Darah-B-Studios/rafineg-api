<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CampayController extends Controller
{
	protected $base_url;
	protected $token;

	public function __construct()
	{
		$this->base_url = 'https://demo.campay.net/api/';
		$this->token = $this->getAccessToken();
	}

	public function getAccessToken()
	{
		$url = $this->base_url . 'token/';
		$params = [
			"username" => env('CAMPAY_USERNAME'),
			"password" => env('CAMPAY_PASSWORD')
		];
		$header = [
			"Content-Type" => "appliction/json"
		];

		$token = Http::headers($header)->post($url, $params);
		return $token;
	}


	public function collect(Request $request, $country_code = '237')
	{
		$url = $this->base_url . 'collect/';
		$data = [
			"amount" => $request->input('amount'),
			"from" => $request->input('phoneNumber'),
			"description" => $request->input('description'),
			"external_reference" => $request->input('externalReference')
		];
		$headers = [
			"Authorization" => "Token " . $this->token,
		];

		$response = Http::asJson()->headers()->post($url, $data);
	}

	public function checkTransactionStatus(transactionCode: string)
	{

	}
}

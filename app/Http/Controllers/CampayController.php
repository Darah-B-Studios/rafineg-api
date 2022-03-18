<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CampayController extends Controller
{
	public $base_url;
	public $token;

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

		$response = Http::acceptJson()->post($url, $params);

		return $response['token'];
	}


	/* public function collect(Request $request, $country_code = '237') */
	public function collect($country_code = '237')
	{
		$url = $this->base_url . 'collect/';
		/* $this->token = $this->getAccessToken(); */
		/* $data = [ */
		/*	"amount" => $request->input('amount'), */
		/*	"from" => $request->input('phoneNumber'), */
		/*	"description" => $request->input('description'), */
		/*	"external_reference" => $request->input('externalReference') */
		/* ]; */

		$data = [
			"amount" => 10,
			"from" => $country_code . '672374414',
			"description" => 'test description',
			"external_reference" => 'test reference'
		];

		$headers = [
			"Authorization" => "Token " . $this->token,
			"Content-Type" => 'application/json'
		];

		$response = Http::withHeaders($headers)->post($url, $data);
		if (!$response->ok()) {
			return response()->json([
				'success' => false,
				'response' => $response->body()
			]);
		}
		$ref = $response['reference'];
		$this->checkTransactionStatus($ref);
	}

	public function checkTransactionStatus(string $reference)
	{
		$url = $this->base_url . 'transaction/' . $reference;
		/* $this->token = $this->getAccessToken(); */

		$headers = [
			"Authorization" => "Token " . $this->token,
		];
		$response = Http::acceptJson()->withHeaders($headers)->get($url);
		return response()->json([
			'status' => $response['status']
		]);
	}
}

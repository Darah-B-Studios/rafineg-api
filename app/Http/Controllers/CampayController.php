<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\CampayCallbackRequest;
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

	public function collect(CollectionRequest $request, $country_code = '237')
	{
		$url = $this->base_url . 'collect/';
		$data = $request->validated();
		$requestData = [
			"amount" => $data['amount'],
			"from" => $data['phoneNumber'],
			"description" => $data['description'],
			"external_reference" => $data['externalReference']
		];
		$headers = [
			"Authorization" => "Token " . $this->token,
		];

		$response = Http::acceptJson()->withHeaders($headers)->post($url, $data);
		if (!$response->ok()) {
			return response()->json([
				'success' => false,
				'message' => 'Transaction failed',
				'response' => $response->body()
			]);
		}
		$ref = $response['reference'];
		$this->checkTransactionStatus($ref);
	}

	public function checkTransactionStatus(string $reference)
	{
		$url = $this->base_url . 'transaction/' . $reference;

		$headers = [
			"Authorization" => "Token " . $this->token,
		];
		$response = Http::acceptJson()->withHeaders($headers)->get($url);
		return response()->json([
			'status' => $response['status']
		]);
	}

	public function callback(CampayCallbackRequest $request)
	{
		// TODO: create callback request
		// TODO: handle callback data and update transaction with ref code
	}
}

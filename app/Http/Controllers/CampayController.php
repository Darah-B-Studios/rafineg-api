<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\CampayCallbackRequest;
use App\Models\CampayTransation;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Request;

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
		$this->token = $this->getAccessToken();

		$data = $request->validated();
		$requestData = [
			"amount" => $data['amount'],
			"from" => $country_code . $data['phoneNumber'],
			"description" => $data['description'],
			"external_reference" => $data['externalReference']
		];
		$headers = [
			"Authorization" => "Token " . $this->token,
		];

		$response = Http::acceptJson()->withHeaders($headers)->post($url, $requestData);
		if (!$response->ok()) {
			return response()->json([
				'success' => false,
				'message' => 'Transaction failed',
				'response' => $response->body()
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'Transaction failed',
			'response' => $response->body()
		]);
		// listen for callback event
		/* return $this->callback(); */
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

	/* public function callback(CampayCallbackRequest $request) */
	public function callback(Request $request)
	{
		// TODO: handle callback data and update transaction with ref code
		/* $data = $request->validated(); */
		/* $url = 'https://rafineg.herokuapp.com/api/callback'; */
		/* $headers = [ */
		/*	"Authorization" => "Token " . $this->token, */
		/* ]; */
		/* $response = Http::acceptJson()->withheaders($headers)->get($url)->json(); */

		return response()->json([
			'success' => 'checking the status of the application',
			'message' => 'callback testing',
			'data' => $request
		]);
		/* return response()->json([ */
		/*	'success' => true, */
		/*	'message' => 'testing', */
		/*	'url' => $url, */
		/*	'token' => $this->token, */
		/*	'data' => $response->body() */
		/* ]); */

		/* if ($response['status'] == 'FAILED') { */
		/*	return response()->json([ */
		/*		'success' => false, */
		/*		'message' => 'The transaction failed', */
		/*		'data' => null */
		/*	]); */
		/* } */

		/* CampayTransation::create($data); */

		// create transaction that is linked to
		// the above campay transaction

		/* $transaction = [ */
		/*	'code' => $response['code'], */
		/*	'amount' => $response['amount'], */
		/*	'status' => $response['status'], */
		/*	'currency' => $response['currency'], */
		/*	'operator' => $response['operator'], */
		/*	'operator_reference' => $response['operator_reference'], */
		/*	'signature' => $response['signature'] */
		/* ]; */

		/* return response()->json([ */
		/*	'success' => true, */
		/*	'message' => 'The transaction succeeded', */
		/*	'data' => $transaction */
		/* ]); */
	}
}

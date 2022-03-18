<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CampayController extends Controller
{
	public function getAccessToken()
	{
		$url = 'https://demo.campay.net/api/token/';
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


	public function pay($method = 'mtn')
	{
		//
	}
}

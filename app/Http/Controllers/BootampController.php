<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BootampController extends Controller
{
    public function collect(Request $request)
    {
        $data = $request->all();
        $headers = [
            "X-MeSomb-Application" => "",
            "X-MeSomb-OperationMode" => "synchronous"
        ];
        $url  = "https://mesomb.hachther.com/api/v1.0/payment/online";

        $response = Http::acceptJson()->withHeaders($headers)->post($url, $data);
        if ($response->successful()) {
            return response()->json([
                "data" => "working"
            ]);
        }
        return response()->json([
            "error" => "Sorry could not process request",
            "data" => []
        ]);
    }
}

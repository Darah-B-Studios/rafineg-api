<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            "amount"                => $data['amount'],
            "from"                  => $country_code . $data['phoneNumber'],
            "description"           => $data['description'],
            "external_reference"    => $data['externalReference']
        ];

        $headers = [
            "Authorization" => "Token " . $this->token,
        ];

        // return json_encode(Auth::user());


        $response = Http::acceptJson()->withHeaders($headers)->post($url, $requestData);
        if (!$response->ok()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Collection request failed',
                'response'  => $response->body()
            ]);
        }


        // create a transaction
        $transaction_data = [
            "amount"                => $data['amount'],
            "phoneNumber"           => $requestData['from'],
            "description"           => $data['description'],
            "externalReference"     => $data['externalReference'],
            'reference'             => $response['reference'],
            'collectionType'        => $data['collectionType'],
            'collectionTypeCode'    => $data['collectionTypeCode'],
            // 'referalCode'           => $data['referalCode']
        ];
        Auth::user()->transactions()->create($transaction_data);

        return response()->json([
            'success' => true,
            'message' => 'Dial ' . $response['ussd_code'] . ' to confirm your request',
            'reference' => $response['reference']
        ]);
    }

    public function checkTransactionStatus(string $reference)
    {
        $url = $this->base_url . 'transaction/' . $reference;

        $headers = [
            "Authorization" => "Token " . $this->token,
        ];

        $response = Http::acceptJson()->withHeaders($headers)->get($url);

        if (!$response->ok()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Verifcation failed',
                'data'      => null
            ]);
        }

        $transaction = Transaction::where('reference', $response['reference'])->first();
        if (!$transaction) {
            return response()->json([
                'success'   => false,
                'message'   => 'Transaction does not exist',
                'data'      => null
            ]);
        }

        $data = [
            'code'                  => $response['code'],
            'currency'              => $response['currency'],
            'operator'              => $response['operator'],
            'operatorReference'     => $response['operator_reference'],
            'status'                => $response['status'],
        ];
        // save transaction

        if (Str::lower($response['status']) == 'failed') {
            $transaction->update();

            return response()->json([
                'success'   => false,
                'message'   => 'Transaction failed',
                'data'      => new TransactionResource($transaction)
            ]);
        }

        if (Str::lower($response['status']) == 'successful') {
            switch ($transaction->collectionType) {
                case config('app.collectionType.registration'):
                    // save user registration
                    Auth::user()->is_registered = true;
                    Auth::user()->save();
                    break;
                case config('app.collectionType.p2p'):
                    $sender = Auth::user();
                    $receiver = User::where('phoneNumber', $transaction['phoneNumber'])->first();
                    $sender->cashbox->balance -= $data['amount'];
                    $receiver->cashbox->balance += $data['amount'];

                    $sender->cashbox->save();
                    $receiver->cashbox->save();
                    break;
                case config('app.collectionType.package'):
                case config('app.collectionType.njangi'):
                case config('app.collectionType.withdrawal'):
                    Auth::user()->cashbox()->balance += $data['amount'];
                    break;
                case config('app.collectionType.other'):
                    break;
            }
        }

        // update transaction status to the current status


        $transaction->status = $response['status'];
        $transaction->code = $response['code'];
        $transaction->currency = $response['currency'];
        $transaction->operator = $response['operator'];
        $transaction->operatorReference = $response['operator_reference'];
        $transaction->update();
        return response()->json([
            'success'   => true,
            'message'   => 'Transaction successfull',
            'data'      => new TransactionResource($transaction)
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
    }

    public function withdraw(WithdrawalRequest $request, $country_code = '237')
    {
        $url = $this->base_url . 'withdraw/';
        $this->token = $this->getAccessToken();
        $data = $request->validated();

        $requestData = [
            "amount" => $data['amount'],
            "to" => $country_code . $data['phoneNumber'],
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

        // create a transaction
        $transaction_data = [
            "amount"                => $data['amount'],
            "phoneNumber"           => $data['phoneNumber'],
            "description"           => $data['description'],
            "externalReference"     => $data['externalReference'],
            'reference'             => $response['reference'],
            'collectionType'        => $data['collectionType'],
            'collectionTypeCode'    => $data['collectionTypeCode'],
        ];

        Auth::user()->transactions()->create($transaction_data);

        return response()->json([
            'success' => true,
            'message' => 'Please wait for confirmation feedback',
            'reference' => $response['reference']
        ]);
    }
}
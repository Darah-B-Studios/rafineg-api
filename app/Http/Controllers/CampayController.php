<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Resources\AppBalanceResource;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
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

        // if transaction is p2p, check if receiver is part of the system
        $receiver = User::where('phone_number', $data['phoneNumber'])->first();
        if (!$receiver) {
            return response()->json([
                "success" => false,
                "message" => "Receiver with this number does not exist in our system",
                "data" => null
            ]);
        }

        $headers = [
            "Authorization" => "Token " . $this->token,
        ];

        $response = Http::acceptJson()->withHeaders($headers)->post($url, $requestData);
        if (!$response || !$response->ok()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Collection request failed',
                'data'  => $response->body()
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
        ];

        Auth::user()->transactions()->create($transaction_data);

        return response()->json([
            'success' => true,
            'message' => 'Dial ' . $response['ussd_code'] . ' to confirm your request',
            'data' => [
                'reference' => $response['reference']
            ]
        ]);
    }

    public function checkTransactionStatus(string $reference)
    {
        $url = $this->base_url . 'transaction/' . $reference;

        $headers = [
            "Authorization" => "Token " . $this->token,
        ];

        $response = Http::acceptJson()->withHeaders($headers)->get($url);

        if (!$response || !$response->ok()) {
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

        $message = '';
        if (Str::lower($response['status']) == 'successful') {
            switch ($transaction->collectionType) {
                case config('app.collectionType.registration'):
                    // save user registration
                    Auth::user()->is_registered = true;
                    Auth::user()->save();

                    // create users cashbox
                    Auth::user()->cashbox()->create([
                        'transaction_id' => $transaction->id,
                        'balance' => 0
                    ]);
                    $message = 'Registration successfull. Welcome to Rafineg';
                    break;
                case config('app.collectionType.p2p'):
                    $sender = Auth::user();
                    $receiver = User::where('phone_number', $transaction['phoneNumber'])->first();

                    if (!$receiver) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No user with such phone number exists in out system',
                            'data' => null
                        ]);
                    }

                    $sender->cashbox->balance -= $transaction['amount'];
                    $receiver->cashbox->balance += $transaction['amount'];

                    $sender->cashbox()->save();
                    $receiver->cashbox()->save();

                    $message = "Successfull transfer of {$transaction['amount']} to {$transaction['phoneNumber']}";
                    break;
                case config('app.collectionType.package'):
                case config('app.collectionType.njangi'):
                case config('app.collectionType.withdrawal'):
                    if (Auth::user()->cashbox->transaction_id != $transaction->id) {
                        $newBalance = Auth::user()->cashbox->balance + $transaction->amount;
                        Auth::user()->cashbox()->update([
                            'balance' => $newBalance,
                            'transaction_id' => $transaction->id
                        ]);

                        $message = "Savings have been registered successfully";
                    } else {
                        $message = "Cashbox not updated";
                    }
                    break;
                case config('app.collectionType.other'):
                    break;
            }
        }

        $transaction->status = $response['status'];
        $transaction->code = $response['code'];
        $transaction->currency = $response['currency'];
        $transaction->operator = $response['operator'];
        $transaction->operatorReference = $response['operator_reference'];
        $transaction->update();

        return response()->json([
            'success'   => true,
            'message'   => $message,
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

    public function withdraw(CollectionRequest $request, $country_code = '237')
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

        // return response()->json($requestData);

        $headers = [
            "Authorization" => "Token " . $this->token,
        ];

        $response = Http::acceptJson()->withHeaders($headers)->post($url, $requestData);
        if (!$response || !$response->ok()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed',
                'data' => $response->body()
            ]);
        }

        // create a transaction
        $transaction_data = [
            "amount"                => $data['amount'],
            "phoneNumber"           => $country_code . $data['phoneNumber'],
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
            'data' => [
                'reference' => $response['reference']
            ]
        ]);
    }

    public function balance()
    {
        $url = $this->base_url . 'balance/';
        $this->token = $this->getAccessToken();

        $headers = [
            "Authorization" => "Token " . $this->token,
        ];

        $response = Http::acceptJson()->withHeaders($headers)->get($url);
        if (!$response || !$response->ok()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Could not get app balance',
                'data'  => $response->body()
            ]);
        }

        $data = [
            'total_balance'  => $response['total_balance'],
            'mtn_balance'    => $response['mtn_balance'],
            'orange_balance' => $response['orange_balance']
        ];

        return response()->json([
            'success'   => false,
            'message'   => 'Could not get app balance',
            'data'  => new AppBalanceResource($data)
        ]);
    }

    public function userTransactions()
    {
        return response()->json([
            'success' => true,
            'message' => 'user transactions',
            'data' => TransactionResource::collection(Auth::user()->transactions)
        ]);
    }
}
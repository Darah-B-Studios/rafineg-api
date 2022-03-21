<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegistrationFeeResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\CreateRegistrationFeeRequest;
use App\Models\RegistrationFee;
use App\Models\User;
use Malico\MeSomb\Payment;
use Throwable;

class RegistrationFeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "success" => true,
            "data" => RegistrationFeeResource::collection(RegistrationFee::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CreateRegistrationFeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRegistrationFeeRequest $request)
    {
        $data = $request->validated();
        //registration fee transaction
        $data['username'] = auth()->user()->name;
        $data['user_id'] = auth()->user()->id;
        $data['amount'] = 10;
        $tel = $data['phone_number'];
        $currentUser = auth()->user();


        $payment_request = new Payment('237672374414', $data['amount']);
        // $payment_request = new Payment($tel, $data['amount']);
        $deposit = $payment_request->pay();



        return response()->json([
            "message" => "Payment failed",
            "data" => json_encode($deposit)
        ]);

        if ($deposit->success) {
            // Fire some event, send payout email
            $currentUser->is_registered = true;
            $currentUser->save();

            $registrationFee = RegistrationFee::create($data);

            return response()->json([
                "data" => [
                    "registrationFee" => new RegistrationFeeResource($registrationFee),
                    "user" => new UserResource($currentUser),
                ],
                "success" => true,
                "message" => "Payment successfull"
            ]);
        }

        /* Registration failed */
        return response()->json([
            "success" => false,
            "message" => "Payment failed",
            "data" => null
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationFee $registrationFee)
    {
        if ($registrationFee->exists()) {
            return response()->json([
                "success" => true,
                "data" => new RegistrationFeeResource($registrationFee),
                "message" => ""
            ]);
        }
        return response()->json([
            "success" => false,
            "data" => null,
            "message" => "RegistrationFee does not exist."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $registrationFee = RegistrationFee::find($id);
        if ($registrationFee->exists()) {
            if ($registrationFee->delete()) {
                return response()->json([
                    "success" => true,
                    "data" => null,
                    "message" => "RegistrationFee deleted successfully"
                ], 201);
            }
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "RegistrationFee could not be deleted"
            ]);
        }
        return response()->json([
            "success" => false,
            "data" => null,
            "message" => "RegistrationFee does not exist"
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferalRequest;
use App\Http\Resources\ReferalResource;
use App\Http\Resources\UserResource;
use App\Models\Referal;
use App\Models\User;

class ReferalController extends Controller
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
            "message" => null,
            "data" => ReferalResource::collection(Referal::all())
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReferalRequest $request)
    {
        $data = $request->validated();
        $referal = Referal::create($data);
        return response()->json([
            "success" => true,
            "message" => "Referal has been created for this user",
            "data" => new ReferalResource($referal)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Referal  $referal
     * @return \Illuminate\Http\Response
     */
    public function show(Referal $referal)
    {
        return response()->json([
            "success" => true,
            "message" => null,
            "data" => new ReferalResource($referal)
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Referal  $referal
     * @return \Illuminate\Http\Response
     */
    public function update(ReferalRequest $request, Referal $referal)
    {
        $data = $request->validated();
        $referal = $referal->update($data);
        return response()->json([
            "success" => true,
            "message" => "Your referal info has been updated",
            "data" => new ReferalResource($referal)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Referal  $referal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Referal $referal)
    {
        $feedback = $referal->delete();
        if ($feedback) {
            return response()->json([
                "success" => true,
                "message" => "Referal has been deleted for this user",
                "data" => new ReferalResource($feedback)
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Referal could not be updated",
            "data" => new ReferalResource($referal)
        ], 404);
    }



    public function userReferals(int $id){
        $user = User::find($id);
        $referals = User::where('referedBy', $user->code)->get();
        if($user) {
        return response()->json([
            'success' => true,
            'message' => 'User referals',
            'data' => UserResource::collection($referals)
        ]);
    }
}


}

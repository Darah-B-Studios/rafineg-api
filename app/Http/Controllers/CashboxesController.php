<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateCashboxRequest;
use App\Http\Requests\UpdateCashboxRequest;
use App\Http\Resources\CashboxResource;
use App\Models\Cashbox;
use Illuminate\Support\Facades\Auth;

class CashboxesController extends Controller
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
            "data" => CashboxResource::collection(Cashbox::all()),
        ]);
    }

    public function getUserCashbox()
    {
        return response()->json([
            "success" => true,
            "data" => new CashboxResource(Auth::user()->cashbox)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCashboxRequest $request)
    {
        $data = $request->validated();
        $casbox = Cashbox::create($data);
        return response()->json([
            "success" => true,
            "data" => new CashboxResource($casbox),
            "message" => "Cashbox was added successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cashbox $cashbox)
    {
        return response()->json([
            "success" => true,
            "data" => new CashboxResource($cashbox),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCashboxRequest $request, Cashbox $cashbox)
    {
        $data = $request->validated();
        if ($cashbox->update($data)) {
            return response()->json([
                "success" => true,
                "data" => new CashboxResource($cashbox),
                "message" => "Cashbox has been updated successfully"
            ]);
        }
        return response()->json([
            "success" => false,
            "data" => new CashboxResource($cashbox),
            "message" => "Error: Cashbox has not been updated!"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cashbox $cashbox)
    {
        if ($cashbox->delete()) {
            return response()->json([
                "success" => true,
                "data" => null,
                "message" => "Cashbox has been deleted successfully"
            ]);
        }
        return response()->json([
            "success" => false,
            "data" => new CashboxResource($cashbox),
            "message" => "An error occured. Cashbox could not be deleted"
        ]);
    }
}
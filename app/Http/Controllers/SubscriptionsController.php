<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Resources\UserResource;
use App\Models\Package;
use App\Models\User;
use Laravel\Cashier\Subscription;
use Throwable;

class SubscriptionsController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => PackageResource::collection(auth()->user()->subscriptions),
            'message' => '',
            ''
        ]);
    }


    public function subscribe($id)
    {
        if (auth()->user()->subscriptions()->find(auth()->user()->id)) {
            return response()->json([
                "message" => "Aleady subscribed to this package",
                "data" => null,
                "success" => false
            ]);
        }

        try {
            $package = Package::find($id);
            auth()->user()->subscriptions()->attach($package->id);
            return response()->json([
                "success" => true,
                "message" => "You have subscribe to this package",
                "data" =>  new PackageResource($package)
            ]);
        } catch (Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage(),
                "data" =>  []
            ]);
        }
    }


    public function unsubscribe($id)
    {

        try {
            $package = Package::find($id);
            auth()->user()->subscriptions()->detach($package);

            return response()->json([
                "success" => true,
                "message" => "You have unscribed to this package",
                "data" => []
            ]);
        } catch (Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage(),
                "data" =>  []
            ]);
        }
    }

    public function subscribers(int $packageId)
    {
        $package = Package::find($packageId);
        if ($package && $package->users()->first()) {
            return response()->json([
                "success" => true,
                "data" => UserResource::collection($package->users),
                "message" => ""
            ]);
        }
        return response()->json([
            "success" => false,
            "data" => null,
            "message" => "Package does no exist"
        ]);
    }

    public function user_subscriptions(int $user_id)
    {
        $user = User::find($user_id);
        return response()->json([
            "success" => true,
            "data" => PackageResource::collection($user->subscriptions),
            "message" => ""
        ]);
    }
}
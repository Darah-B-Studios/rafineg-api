<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\CashboxResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
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
            "data" => UserResource::collection(User::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        if (str_contains($data['name'], " ")) {
            $names = explode(' ', $data['name']);
            $data["firstname"] = $names[0];
            $data["lastname"] = $names[1];
        } else {
            $data['firstname'] = $data['name'];
        }

        $user = User::create($data);
        return response()->json([
            "success" => true,
            "data" => new UserResource($user),
            "message" => "User was added successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            "success" => true,
            "data" => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // create user profile
        $profile_data = [
            'bio' => $request->bio,
            'address' => $request->address,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'image'  => $request->image,
        ];

        // create user profile
        $user->profile()->update($profile_data);
        $feedback = $user->update($data);
        if ($feedback) {
            return response()->json([
                "success" => true,
                "data" => [new UserResource($user), $profile_data],
                "message" => "User has been updated successfully"
            ]);
        }

        return response()->json([
            "success" => false,
            "data" => new UserResource($user),
            "message" => "Error: user has not been updated!"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return response()->json([
                "success" => true,
                "data" => null,
                "message" => "User has been deleted successfully"
            ]);
        }
        return response()->json([
            "success" => false,
            "data" => new UserResource($user),
            "message" => "An error occured. User could not be deleted"
        ]);
    }


    public function cashbox()
    {
        return response()->json([
            "success" => true,
            "data" => new CashboxResource(Auth::user()->cashbox),
            "message" => "user cashbox data"
        ]);
    }
}
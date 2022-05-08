<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Mail\ForgotPasswordEmail;
use App\Mail\NewPasswordEmail;
use App\Models\ForgotPassword;
use App\Models\User;
use App\Models\Referal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Signup new user in the app
     *
     * @param App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function signup(UserRequest $request)
    {
        $data = $request->validated();
        $data['code'] = $this->generateUserCode();
        $data['password'] =  Hash::make($data['password']);

        if ($new_user = User::create($data)) {
            // create user profile
            $profile_data = [
                'bio' => '',
                'address' => '',
                'gender' => 'male',
                'date_of_birth' => Carbon::now(),
                'image'  => '',
            ];

            // create user profile
            $new_user->profile()->create($profile_data);

            return response()->json([
                "success" => true,
                "message" => "You account has been successfully created",
                "data" => []
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "An error occured. Try again later",
                "data" => []
            ]);
        }
    }

    /**
     * Login user into app
     *
     * @param App\Http\Requests\LoginUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function signin(LoginUserRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!Auth::attempt($data)) {
            return response()->json([
                "success" => false,
                "message" => "Credentials do not match"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Login successful",
            "accessToken" => $user->createToken('user token')->plainTextToken,
            "data" => new UserResource($user)
        ]);
    }

    public function forgot_password(Request $request)
    {
        $data = $request->validate([
            "email" => "required|email"
        ]);

        // check if there exist a record for reset password and delete it
        $prevForgotPasswordRecord = ForgotPassword::where('email', $request->email)->get();
        if (count($prevForgotPasswordRecord) == 1) {
            ForgotPassword::where('email', $request->email)->delete();
        }

        $data['verification_code'] = $this->generateUserCode();
        ForgotPassword::create($data);

        Mail::to($data['email'])->send(new ForgotPasswordEmail($data['verification_code']));

        return response()->json([
            "success" => true,
            "message" => "Password reset code has been sent to " .  $data['email'],
            "data" => [
                "email" => $data['email']
            ]
        ]);
    }

    /**
     * Confirm password reset code
     * @param request: Contains the emal address for the
     * app to send reset code
     * @return apiResponse to client
     */
    public function confirm_password_reset_code(string $code)
    {
        $response = [
            "success" => false,
            "message" => null,
            "data" => null,
        ];
        // $code = $request->only['code'];
        // return json_encode($code);
        $forgotPassword = ForgotPassword::where("verification_code", $code)->first();
        $user = User::where('email', $forgotPassword->email)->first();

        // return response()->json($forgotPassword->email);

        if ($forgotPassword->is_used == false) {
            $forgotPassword->is_used = true;
            $forgotPassword->update();

            // reset current user password
            $newPassword = $this->generateUserCode(8);
            $user->update(["password" => Hash::make($newPassword)]);

            // send new password email
            Mail::to($user->email)
                ->send(new NewPasswordEmail($newPassword, $user->lastname));

            $response['success'] = true;
            $response['message'] = "Code verified successfully. Check your email for a new password";
            return response()->json($response);
        } elseif ($forgotPassword->expires_on < Carbon::now()) {
            $response['message'] = "Reset code has expired";
            return response()->json($response);
        } else {
            $forgotPassword->is_used = true;
            $response['message'] = "Reset link verified successfully";
            $response['success'] = true;
            return response()->json($response);
        }
    }

    /**
     * Confirm password reset code
     * @param request (mixed) Contains the new password
     * and the confirme password
     * @return apiResponse to client
     */
    public function reset_password(Request $request)
    {
        $data = $request->validate([
            "oldPassword" => "required|min:6",
            'newPassword' => 'min:6|required_with:confirmPassword|same:confirmPassword',
            'confirmPassword' => 'min:6|string',
            "email" => "required|email"
        ]);

        $user = User::where('email', $data['email'])->first();
        // echo json_encode($user);
        if ($user) {
            $newPassword = Hash::make($data['newPassword']);
            $user->update(["password" => $newPassword]);
            return response()->json([
                "success" => true,
                "errors" => [],
                "message" => "Password for " . $data['email'] . " has been reset successfully",
                "data" => new UserResource($user)
            ]);
        }
    }

    public function change_password(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|string|min:6',
            'password_confirm' => 'required|confirmed:password',
            'email' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // set user password to new password
            $user->password = Hash::make($data['password']);
            $user->save();

            return response()->json([
                "message" => "Password reset successfully",
                "data" => null,
                "success" => true
            ]);
        }
    }

    /**
     * Handle phone and client email verifications
     * @param method
     */
    public function verify_client(string $method)
    {
        if ($method == 'email') {
            // handle email verification
        } else if ($method == 'phone') {
            // handle phone number verification
        } else {
            // throw verification error
        }
    }

    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();
        $request->user()->tokens()->delete();
        // auth()->user()->currentAccessToken()->delete();
        return response()->json([
            "success" => true,
            "message" => "Logout successful"
        ]);
    }

    private function generateUserCode(int $length = 6)
    {
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result), 0, $length);
    }
}
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\UserResetPassword;
use App\PasswordReset;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Validator;

class ForgotPasswordController extends Controller
{


    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'email'       => 'required|email|exists:users,email,active,1,blocked_at,NULL,deleted_at,NULL',
            ]);

        if(!$validator->fails()){
            $user = User::whereEmail($request->email)->whereActive(1)->first();
            $otp = strtoupper(str_random(6));
            $token = $this->generateToken($otp);
            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => now()
                 ]
            );

            $user->notify(new UserResetPassword($otp));

            if($passwordReset){
                $response_data = ["success" => 1, "message" => __("site.otp_emailed"), "otp" => $otp];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" => 0, "message" => __("passwords.user")];
        }
        return response()->json($response_data);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkResetToken(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'email'       => 'required|email',
              'token'       => 'required',
            ]);

        if(!$validator->fails()){
            $expiration = config("auth.passwords.users.expire");
            $token = $this->generateToken($request->token);
            $tokenReset = PasswordReset::whereToken($token)->whereEmail($request->email)->first();
            if($tokenReset){
                $expired = Carbon::parse($tokenReset->created_at)->addSeconds($expiration)->isPast();
                if(!$expired){
                    $user = User::whereEmail($request->email)->whereActive(1)->first();
                    $token = $user->createToken('FunClub')->accessToken;
                    $response_data = ["success" => 1, "message" => __("validation.verified_success", ["attr" => "OTP"]), "token" => $token];
                }else{
                    $response_data = ["success" => 0, "message" => __("validation.token_expired", ["attr" => "Password reset"])];
                }
            }else{
                $response_data = ["success" => 0, "message" => __("validation.invalid_value", ["attr" => "token"])];
            }

        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        
        return response()->json($response_data);
    }

    private function generateToken($value)
    {
        $key = config('app.key');

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        return hash_hmac('sha256', $value, $key);
    }
}

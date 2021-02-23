<?php

namespace App\Http\Controllers\PrivateArea\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('private.auth.login');
    }

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
              'email'       => 'required|email|exists:admin_users,email,active,1,deleted_at,NULL',
            ]);

    	if(!$validator->fails()){
    		// We will send the password reset link to this user. Once we have attempted
    		// to send the link, we will examine the response then see the message we
    		// need to show to the user. Finally, we'll send out a proper response.
    		$response = $this->broker()->sendResetLink(
    		    $request->only('email')
    		);

    		if($response == Password::RESET_LINK_SENT){
    			$response_data = ["success" => 1, "message" => __("passwords.sent")];
    		}else{
    			$response_data = ["success" => 0, "message" => __("site.server_error")];
    		}
    	}else{
    		$response_data = ["success" => 0, "message" => __("passwords.user")];
    	}
        return response()->json($response_data);
    }
}

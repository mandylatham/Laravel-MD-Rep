<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\SocialToken;
use App\DeviceToken;
use App\PasswordReset;
use Illuminate\Support\Str;
use App\UserNotification;
use App\Follower;
use App\UserBlock;
use App\Events\LoginEvent;
use Illuminate\Support\Facades\Hash;
use Avatar;
//use Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Notifications\UserEmailVerification;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $new_request = $request->all();
        if($request->gender != 0){
            $new_request["gender"] = (int)$request->gender;
        }else{
            $new_request["gender"] = "";
        }
        Log::debug($new_request);
        $validator = Validator::make($new_request,
            [
              'name'       	=> 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|not_exists:users,email',
              'phone_number'=> 'nullable|numeric|digits:'.limit("phone.max").'|not_exists:users,phone',
              'tel_code'    => 'required|in:+1',
              
              'password'    => 'required|min:'.limit("password.min").'|max:'.limit("password.max").'|password',
              'profile_pic' => 'nullable|image|mimes:'.limit("profile_pic.format").'|min:'.limit("profile_pic.min").'|max:'.limit("profile_pic.max").',|dimensions:min_width='.limit("profile_pic.min_width").',min_height='.limit("profile_pic.min_height"),
              'gender'      => 'nullable|integer|exists:genders,id,active,1,deleted_at,NULL',
              'device_type'   => 'required|exists:device_types,code,mobile_type,1,active,1,deleted_at,NULL'
            ], ["phone_number.not_exists" => "Phone number is already used by another account",
                "email.not_exists" => "Email address is already used by another account"]);

 		if(!$validator->fails()){
            $filePath = "images/profile";
            if($request->hasFile('profile_pic')){
                $file = $request->file('profile_pic')->store($filePath);
                $file = Storage::url($file);
                //$file = "storage/".$file;
            }else{
                // $file =  "storage/".$filePath.uniqid().".png";
                // Avatar::create(strtoupper($request->name))->save($file, $quality = 90);
                $upper = strtoupper($request->name);
                $file =  $filePath."/".substr($upper, 0, 2).".png";
                $file1 =  "storage/".$file;
                
                Avatar::create($upper)->save($file1, $quality = 90);
                //$contents = Storage::exists(public_path()."/".$file1);
                //$file = Storage::disk('s3')->put($file1,$contents);
                $file = url($file1);
                //dd($file);
            }

            $otp = strtoupper(str_random(6));
            $token = $this->generateToken($otp);

 			$user = new User();
 			$user->name 		  = $request->name;
 			$user->email 	      = $request->email;
 			$user->password 	  = bcrypt($request->password);
 			$user->phone 	      = $request->filled("phone_number") ? $request->phone_number : "";
 			$user->tel_code 	  = $request->tel_code;
 			$user->gender 	      = $request->gender != 0 || !empty($request->gender) ? $request->gender : 3;
 			$user->profile_pic    = $file;
 			$user->active 	      = 0;
 			$user->created_by     = 1;
 			$user->updated_by     = 1;
            $user->token = $token;
            

 			if($user->save()){
	 			$token = $user->createToken('FunClub')->accessToken;
                $user->notify(new UserEmailVerification($otp));
                //event(new LoginEvent($user, $request->ip(), $request->device_type));
 				$response_data = ["success" => 1, "message" => __("validation.activation_email")];
 			}else{
 				$response_data = ["success" => 0, "message" => __("site.server_error")];
 			}
 			
 		}else{
 			$response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
 		}
        Log::debug($response_data);
 
        return response()->json($response_data);
    }


    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialRegister(Request $request)
    {
        $socialProvider = $request->provider;
        $validator = Validator::make($request->all(),
            [
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max"),
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|not_exists:users,email',
              'phone_number' => 'nullable|numeric|digits:'.limit("phone.max").'|not_exists:users,phone',
              'tel_code'    => 'nullable|in:+1',
              'profile_pic' => 'nullable|image|mimes:'.limit("profile_pic.format").'|min:'.limit("profile_pic.min").'|max:'.limit("profile_pic.max").',|dimensions:min_width='.limit("profile_pic.min_width").',min_height='.limit("profile_pic.min_height"),
              'gender'      => 'nullable|exists:genders,id,active,1,deleted_at,NULL',
              'code'        => 'required|min:'.limit("social_token.min").'|max:'.limit("social_token.max").'|not_exists:social_tokens,code,active,1,provider,'.$socialProvider.',deleted_at,NULL',
              'provider'    => ['required', Rule::in(config("site.social_providers"))],
              'device_type'   => 'required|exists:device_types,code,mobile_type,1,active,1,deleted_at,NULL'
            ], ["phone_number.not_exists" => "Phone number is already used by another account",
                "email.not_exists" => "Email address is already used by another account"]
        );
        if(!$validator->fails()){

        	$filePath = "images/profile/";
            // if($request->hasFile('profile_pic')){
            //     //$file = $request->file('profile_pic')->store($filePath);
            //     $file = $request->file('profile_pic')->store($filePath);
            //     //$file = "storage/".$file;
            // }else{
            //     // $file =  "storage/".$filePath.uniqid().".png";
            //     // Avatar::create(strtoupper($request->name))->save($file, $quality = 90);

            //     $file =  $filePath.uniqid().".png";
            //     $file1 =  "storage/".$file;
                
            //     Avatar::create(strtoupper($request->name))->save($file1, $quality = 90);
            //     $contents = Storage::disk("public")->get(public_path().$file1);
            //     $file = Storage::disk('s3')->put($file,$contents);
            // }

            if($request->hasFile('profile_pic')){
                $file = $request->file('profile_pic')->store($filePath);
                $file = Storage::url($file);
                //$file = "storage/".$file;
            }else{
                // $file =  "storage/".$filePath.uniqid().".png";
                // Avatar::create(strtoupper($request->name))->save($file, $quality = 90);
                $upper = strtoupper($request->name);
                $file =  $filePath."/".substr($upper, 0, 2).".png";
                $file1 =  "storage/".$file;
                
                Avatar::create($upper)->save($file1, $quality = 90);
                //$contents = Storage::exists(public_path()."/".$file1);
                //$file = Storage::disk('s3')->put($file1,$contents);
                $file = url($file1);
            }

            $user = new User();
            $user->name           = $request->name;
            $user->email          = $request->email;
            $user->password       = "";
            $user->phone          = $request->filled("phone_number") ? $request->phone_number : "";
            $user->tel_code       = $request->filled("tel_code") ? $request->tel_code : "";
            $user->gender         = $request->gender;
            $user->profile_pic    = $file;
            $user->active         = 1;
            $user->created_by     = 1;
            $user->updated_by     = 1;


            if($user->save()){
                $socialToken = new SocialToken();
                $socialToken->user_id = $user->id;
                $socialToken->code = $request->code;
                $socialToken->provider = $request->provider;
                $socialToken->active = 1;
                $socialToken->created_by = 1;
                $socialToken->updated_by = 1;
                if($socialToken->save()){
                    $token = $user->createToken('FunClub')->accessToken;
                    event(new LoginEvent($user, $request->ip(), $request->device_type));
                    $response_data = ["success" => 1, "token" => $token, "data" => $user];
                }else{
                    $response_data = ["success" => 0, "message" => __("site.server_error")];
                }
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        
 
        return response()->json($response_data);
    }


    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendConfirmationEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'email'       => 'required|email|exists:users,email,deleted_at,NULL',
            ]);

        if(!$validator->fails()){
            $user = User::whereEmail($request->email)->first();
            if($user->active != 1){
                $otp = strtoupper(str_random(6));
                $token = $this->generateToken($otp);
                $user->token = $token;
                $user->save();
                $user->notify(new UserEmailVerification($otp));
                if($user){
                    $response_data = ["success" => 1, "message" => __("site.otp_emailed"), "otp" => $otp];
                }else{
                    $response_data = ["success" => 0, "message" => __("site.server_error")];
                }
            }else{
                $response_data = ["success" => 1, "message" => __("validation.user_active")];
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
    public function checkVerificationToken(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'email'       => 'required|email',
              'token'       => 'required',
            ]);

        if(!$validator->fails()){
            $token = $this->generateToken($request->token);
            $user = User::whereToken($token)->whereEmail($request->email)->first();
            if($user){
            	$user->token   = NULL;
                $user->active   = 1;
            	$user->save();
                $token = $user->createToken('FunClub')->accessToken;
                $response_data = ["success" => 1, "message" => __("validation.verified_success", ["attr" => "Email"]), "token" => $token, "data" => $user];
                
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

    /**
     * Handles Social Login Check
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialCheck(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max"),
              'code'        => 'required|max:200',
              'provider'    => ['required', Rule::in(config("site.social_providers"))],
              'device_type'   => 'required|exists:device_types,code,mobile_type,1,active,1,deleted_at,NULL'
            ]);
        if(!$validator->fails()){
            $user = User::whereEmail($request->email)->first();
            $social_token = SocialToken::whereCode($request->code)->whereProvider($request->provider)->whereActive(1)->first();
            if($user){
            	if($social_token && $social_token->user_id != $user->id){
            		$response_data = ["success" => 0, "register" => 0, "message" => __("validation.not_exists", ["Attribute" => "Social Code"])];
            	}else{
            		if(empty($user->blocked_at)){
	            		if(!$social_token){
	            			if($user->active == 0){
	            				$user->active = 1;
	            				$user->token = NULL;
	            				$user->save();
	            			}
	            			$socialToken = new SocialToken();
	            			$socialToken->user_id = $user->id;
	            			$socialToken->code = $request->code;
	            			$socialToken->provider = $request->provider;
	            			$socialToken->active = 1;
	            			$socialToken->created_by = $user->id;
	            			$socialToken->updated_by = $user->id;
	            			$socialToken->save();
	            		}

		                $token = $user->createToken('FunClub')->accessToken;
	                    event(new LoginEvent($user, $request->ip(), $request->device_type));
		                $response_data = ["success" => 1, "register" => 1, "token" => $token, "data" => $user];
            		}else{
            			 $response_data = ["success" => 0, "register" => 1, "message" =>  __("validation.account_blocked")];
            		}
            	}
            }else{
                $response_data = ["success" => 0, "register" => 0, "message" => __("validation.not_found", ["attr" => "User"])];
                /*if($social_token){
                    $response_data = ["success" => 0, "register" => 0, "message" => __("validation.not_exists", ["Attribute" => "Social Code"])];
                }else{
            	}*/
            }
            
        }else{
            $response_data = ["success" => 0, "register" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        
 
        return response()->json($response_data);
    }
 
    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'email'       => 'required|email',
              'password'   => 'required',
              'device_type'   => 'required|exists:device_types,code,mobile_type,1,active,1,deleted_at,NULL'
            ]);

        if(!$validator->fails()){
            $credentials = [
                'email'     => $request->email,
                'active'    => 1
            ];

            $user = User::whereEmail($credentials)->first();

            if ($user) {
                if(empty($user->blocked_at)){
                    if (Hash::check($request->password, $user->password)) {
                    	if($user->active == 1){
    	                    $token = $user->createToken('FunClub')->accessToken;
    	                    event(new LoginEvent($user, $request->ip(), $request->device_type));
    	                    $response_data = ["success" => 1,  "token" => $token, "data" => $user];
                    	}else{
                            $otp = strtoupper(str_random(6));
                            $token = $this->generateToken($otp);
                            $user->notify(new UserEmailVerification($otp));
                    		$response_data = ["success" => 2,  "message" => __("validation.not_verified", ["attr" => "Email address"])];
                    	}
                    } else {
                        $response_data = ["success" => 0,  "message" => __("site.invalid_login")];
                    }
                }else{
                    $response_data = ["success" => 0,  "message" => __("validation.account_blocked")];
                }

            } else {
               $response_data = ["success" => 0,  "message" => __("site.invalid_login")];
            }

        }else{
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }
 
    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $response_data = ["success" => 1, 'data' => auth()->user()];
        return response()->json($response_data);
    }


    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileUpdate(Request $request)
    {
    	Log::debug($request->hasFile("profile_pic"));
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|unique:users,email,'.$user->id.',id',
              'phone_number'       => 'required|numeric|digits:'.limit("phone.max").'|unique:users,phone,'.$user->id.',id',
              'tel_code'    => 'required|in:+1',
              'profile_pic' => 'nullable|image|mimes:'.limit("profile_pic.format").'|max:'.limit("profile_pic.max"),
              'gender'      => 'required|exists:genders,id,active,1,deleted_at,NULL',
              'bio'         => 'nullable|string|min:'.limit("bio.min").'|max:'.limit("bio.max"),
              //'device_type'   => 'required|exists:device_types,code,mobile_type,1,active,1,deleted_at,NULL'
            ], ["phone_number.not_exists" => "Phone number is already used by another account",
                "email.not_exists" => "Email address is already used by another account"]);
        if(!$validator->fails()){

        	$filePath = "images/profile";
            if($request->hasFile('profile_pic')){
                $user->profile_pic = Storage::url($request->file('profile_pic')->store($filePath));
            }

            $user->name        = $request->name;
            $user->email       = $request->email;
            $user->phone       = $request->phone_number;
            $user->tel_code    = $request->tel_code;
            $user->gender      = $request->gender;
            $user->bio         = $request->bio ? $request->bio : "";
            $user->updated_by  = $user->id;

            if($user->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Profile"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }

    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                "current_password"  => "required",
                'password'          => 'required|confirmed|min:'.limit("password.min").'|max:'.limit("password.max").'|password|different:current_password',
            ]);

        if(!$validator->fails()){
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->updated_by = $user->id;
                if($user->save()){
                    $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Password"])];
                }else{
                    $response_data = ["success" => 0, "message" => __("site.server_error")];
                }
            }else{

                $response_data = ["success" => 0,  "message" => __("site.invalid_password")];
            }
        }else{
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }


    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fcmUpdate(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'fcm_code'    => 'required|min:'.limit("fcm_code.min").'|max:'.limit("fcm_code.max"),
              'device_type' => 'required|exists:device_types,id,active,1,deleted_at,NULL',
            ]);

        if(!$validator->fails()){

            $defaultValue = ['user_id' => $user->id, 'code' => $request->fcm_code, 'device_type' => $request->device_type];
            $otherValues = ['created_by' => $user->id, 'updated_by' => $user->id];
            $deviceToken = DeviceToken::updateOrCreate($defaultValue, $otherValues);

            if($deviceToken){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "FCM Code"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }

        /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                'password'  => 'required|confirmed|min:'.limit("password.min").'|max:'.limit("password.max").'|password',
            ]);

        if(!$validator->fails()){
            $user = auth()->user();

            $user->password = bcrypt($request->password);
            $user->updated_by = $user->id;
            if($user->save()){
                PasswordReset::whereEmail($user->email)->delete();
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Password"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }

        }else{
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }


        /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function logout(Request $request)
    {
        
        $user = auth()->user();
        $user->token()->revoke();

        $response_data = ["success" => 1, "message" => __("site.logout_success")];

        return response()->json($response_data);
    }

    public function userBlock(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'block_user' => 'required|numeric',
              'status'  => 'required|in:0,1',
            ]);

        if (!$validator->fails()) 
            {
                $user = auth()->user();
                if($request->block_user != $user->id )
                {
                    if($request->status !=1)
                    {
                        $checking = UserBlock::whereUserId($user->id)->whereBlockUser($request->block_user)->first();
                        if($checking)
                        {
                        	
                            $unblock = UserBlock::whereUserId($user->id)->whereBlockUser($request->block_user);
                            $data   = ['status' => 1];
                            $unblock->update($data);
                            $delete = Follower::whereFollowerId($user->id)->whereFollowedId($request->block_user)->delete();
                            $response_data = ["success" => 1, "message" => __("validation.block_success", ["attr" => "User blocked"])];
                        }
                        else
                        {
                            $block              = new UserBlock();
                            $block->user_id     = $user->id;
                            $block->block_user  = $request->block_user;
                            $block->status      = $request->status;
                            $block->created_by  = $user->id;
                            $block->updated_by  = $user->id;
                            $block->save(); 
                            $delete = Follower::whereFollowerId($user->id)->whereFollowedId($request->block_user)->delete();
                            $response_data = ["success" => 1, "message" => __("validation.block_success", ["attr" => "User blocked"])];
                        }  
                    }
                    else
                    {
                        $checking = UserBlock::whereUserId($user->id)->whereBlockUser($request->block_user)->first();
                        if($checking)
                        {
                            $unblock = UserBlock::whereUserId($user->id)->whereBlockUser($request->block_user);
                            $data   = ['status' => 0];
                            $unblock->update($data);
                        }
                        $update = Follower::whereFollowerId($user->id)->whereFollowedId($request->block_user);
                        if($update)
                        {
                            $data   = ['deleted_at' => NULL];
                            $update->update($data);
                            $response_data = ["success" => 1, "message" => __("validation.block_success", ["attr" => "User unblocked"])]; 
                        }
                    }
                }else
                {
                    $response_data = ["success" => 1, "message" => 'Same user account cannot be blocked'];
                }
                
            }
            else
            {
                $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
            }

          return response()->json($response_data);
    }

    public function blockList(Request $request)
    {
        /*$data= UserBlock::with(["blockUser"])->whereUserId(Auth::id())->get();*/
        $query = UserBlock::select(["block_user"])->whereStatus(1)->whereUserId(Auth::id())->get();
        $user = $query->pluck("block_user")->toArray();
        $limit = config("site.pagination.block");
        $data=User::select("id", "name", "gender","profile_pic")->whereNull("blocked_at")->whereActive(1)->whereIn('id',$user)->paginate($limit);
        $response_data = ["success" => 1, 'data' => $data];
        return response()->json($response_data);
    }

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(),
                    [
                      "lat"           => "required|numeric",
                      "long"          => "required|numeric",
                    ]);

        if (!$validator->fails()){
            $user = auth()->user();
            $user->lat = $request->lat;
            $user->lng = $request->long;
            if($user->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Location"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }


}

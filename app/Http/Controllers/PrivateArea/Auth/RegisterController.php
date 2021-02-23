<?php

namespace App\Http\Controllers\PrivateArea\Auth;

use App\AdminUser;
use App\AdminUserType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->redirectTo = config("site.redirectTo");
        $this->middleware('guest');
    }
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('private.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if(!$validator->fails())
        {
            event(new Registered($user = $this->create($request->all())));
            if($this->registered($request, $user))
            {
                $response_data = ["success" => 1, "message" => __("validation.create_success", ["attr" => "Account"])];
            }
            else
            {
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
        }
        return response()->json($response_data);
        
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|not_exists:admin_users,email',
              'phone'       => 'required|numeric|digits:'.limit("phone.max").'|not_exists:admin_users,phone',
              'password'    => 'required|min:'.limit("password.min").'|max:'.limit("password.max").'|password'
            ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $code = AdminUserType::whereCode('club')->first();
        return AdminUser::create([
            'name' 			=> $data['name'],
            'email' 		=> $data['email'],
            'phone' 		=> $data['phone'],
            'user_type' 	=> $code->id,
            'tel_code'      => +1,
            'created_by'    => 1, 
            'active' 	    => 0, 
            'updated_by' 	=> 1,
            'password' 		=> Hash::make($data['password']),
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
        if(AdminUser::whereEmail($request->email)->first())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

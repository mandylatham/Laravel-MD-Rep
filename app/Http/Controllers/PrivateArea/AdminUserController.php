<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Yajra\Datatables\Datatables;
use App\AdminUser;
use App\AdminUserType;
use Illuminate\Support\Facades\Hash;
use Validator;
use Avatar;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    public function index()
    {
        $response_data["title"] = __("title.private.employee_list");
        $response_data["userTypes"] = AdminUserType::whereAdminType(2)->whereActive(1)->get();
        return view("private.employee.list")->with($response_data);
    }

    public function getList(Request $request)
    {
        $query = AdminUser::whereHas('userType', function ($query) {
                    $query->where('admin_type', '=', 2);
                });
        
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }
        if($request->has("user_type") && $request->user_type != ""){
            $query->whereUserType($request->user_type);
        }
       return Datatables::of($query->get())->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $new_request = $request->all();
        $validator = Validator::make($new_request,
            [
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|not_exists:admin_users,email',
              'phone'       => 'required|numeric|digits:'.limit("phone.max").'|not_exists:admin_users,phone',
              'password'    => 'required|min:'.limit("password.min").'|max:'.limit("password.max").'|password',
              'user_type'   => 'required|exists:admin_user_types,id,admin_type,2,active,1,deleted_at,NULL'
            ]);

        if(!$validator->fails()){
       
            $adminUser = new AdminUser();
            $adminUser->name           = $request->name;
            $adminUser->email          = $request->email;
            $adminUser->password       = Hash::make($request->password);
            $adminUser->phone          = $request->phone;
            $adminUser->tel_code       = "+1";
            $adminUser->user_type      = $request->user_type;
            $adminUser->active         = 1;
            $adminUser->created_by     = 1;
            $adminUser->updated_by     = 1;
            
            $filePath = "images/profile/";
            $file =  "storage/".$filePath.uniqid().".png";
            //Avatar::create(strtoupper($request->name))->save($file, $quality = 90);

            Avatar::create(strtoupper($request->name))->save($fileLocal, $quality = 90);
            $contents = Storage::disk("local")->get($filePath);
            $file = Storage::disk('s3')->put($filePath,$contents);


            $adminUser->profile_pic    = url($file);
            $adminUser->save();
            $insertedId = $adminUser->id;
            $adminUser->code           = hash('md5',"ADMIN_000".$insertedId);
            if($adminUser->save()){
                $response_data = ["success" => 1,"message" => __("validation.create_success",['attr'=>'User'])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            } 
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
        }
        //Log::debug($response_data);
    
        return response()->json($response_data);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:admin_users,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $userId = $request->data;
            $user = AdminUser::where('id', $userId)->first();
            if($user)
            {

                $response_data = ["success" => 1,  "data" => $user];
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $userId = $request->data;
        $new_request = $request->all();
        //Log::debug($new_request);
        $validator = Validator::make($new_request,
            [
              'data'        => 'required|exists:admin_users,id,deleted_at,NULL',
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|unique:admin_users,email,'.$userId.',id,deleted_at,NULL',
              'phone'       => 'required|numeric|digits:'.limit("phone.max").'|unique:admin_users,phone,'.$userId.',id,deleted_at,NULL',
              'password'    => 'nullable|min:'.limit("password.min").'|max:'.limit("password.max").'|password',
              'user_type'   => 'required|exists:admin_user_types,id,admin_type,2,active,1,deleted_at,NULL'
            ]);

        if(!$validator->fails()){
            
            $adminUser = AdminUser::whereId($request->data)->first();
            $adminUser->name           = $request->name;
            $adminUser->email          = $request->email;
            if(!empty($request->password)){
                $adminUser->password   = Hash::make($request->password);
            }
            $adminUser->phone          = $request->phone;
            $adminUser->user_type      = $request->user_type;
            $adminUser->updated_by     = 1;

            if($adminUser->save()){
                $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'User'])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            } 
        
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
        }
       
        //Log::debug($response_data);
    
        return response()->json($response_data);
    }

    public function myProfile(Request $request)
    {
        $response_data["title"] = __("title.private.my_profile");
        $response_data["profile"] = auth()->user();
        return view("private.employee.myprofile")->with($response_data);
    }

    public function passwordUpdate(Request $request)
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

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|unique:users,email,'.$user->id.',id,active,1,deleted_at,NULL',
              'phone'       => 'required|numeric|digits:'.limit("phone.max").'|unique:users,phone,'.$user->id.',id,active,1,deleted_at,NULL',
              'profile_pic' => 'nullable|image|mimes:'.limit("profile_pic.format").'|max:'.limit("profile_pic.max"),
            ]);
        if(!$validator->fails()){

        	$filePath = "images/private/profile/";
            if($request->hasFile('profile_pic')){
                $user->profile_pic = Storage::url($request->file('profile_pic')->store($filePath));
            }

            $user->name        = $request->name;
            $user->email       = $request->email;
            $user->phone       = $request->phone;
            $user->tel_code    = +1;
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

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "value" => "required|in:0,1",
                    "pk" => "required|exists:admin_users,id",
                ]);
        if(!$validator->fails()){
            $user = AdminUser::find($request->pk);
            $user->active = $request->value;
            if($user->save()){
                $response_data = ["success" => 1, "message" => __("validator.update_success", ["attr" => "Employee Status"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }

    //Employee Delete
    public function destroy(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:admin_users,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $user = AdminUser::find($request->data); 
            if(!$user->isAdmin()){
                if($user->delete()){
                    $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Employee'])]; 
                }else{
                    
                    $response_data =  ['success' => 1, 'message' => __('site.server_error')]; 
                }    
            }else{
                $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
            }

        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function logout()
    {
    	Auth::logout();
        return redirect(route("login"));
    }
}

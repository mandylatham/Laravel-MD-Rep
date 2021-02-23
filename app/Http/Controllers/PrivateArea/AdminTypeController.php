<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use App\User;
use App\UserPost;
use App\Gender;
use App\AdminUserType;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminTypeController extends Controller
{
    
    public function index()
    {
        $response_data["title"] = __("title.private.admin_type_list");
        return view("private.permission.userType")->with($response_data);
    }

    public function getUserTypeList(Request $request)
    {
        $query = AdminUserType::whereAdminType(2);
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
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
        //Log::debug($new_request);
        $validator = Validator::make($new_request,
            [
                'name'          => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string|not_exists:admin_user_types,name',
            ]);

        if(!$validator->fails()){
       
            $userType = new AdminUserType();
            $userType->name           = $request->name;
            $userType->code           = strtolower(str_replace(' ', '', trim($request->name)));
            $userType->admin_type     = 2;
            $userType->active         = 1;
            $userType->created_by     = Auth::id();
            $userType->updated_by     = Auth::id();
            if($userType->save()){
                $response_data = ["success" => 1,"message" => __("validation.create_success",['attr'=>'User Type'])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
        }
    
        return response()->json($response_data);
    }


    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "value" => "required|in:0,1",
                    "pk" => "required|exists:admin_user_types,id",
                ]);
        if(!$validator->fails()){
            $userType = AdminUserType::find($request->pk);
            $userType->active = $request->value;
            if($userType->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Status"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
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
              'data' => 'required|exists:admin_user_types,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $typeId = $request->data;
            $userType = AdminUserType::where('id', $typeId)->first();
            if($userType)
            {
                $response_data = ["success" => 1, "data" => $userType];
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
    
        $typeId = $request->data;
        $new_request = $request->all();
        $validator = Validator::make($new_request,
            [
                'data' => 'required|exists:admin_user_types,id,deleted_at,NULL',
                'name' => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string|unique:admin_user_types,name,'.$typeId.',id,deleted_at,NULL',
            ]);

        if(!$validator->fails()){
            
           $userType = AdminUserType::find($typeId);
           $userType->name           = $request->name;
           $userType->code           = strtolower(str_replace(' ', '', trim($request->name)));
           $userType->updated_by     = Auth::id();

            if($userType->save()){
                $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'User type'])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            } 
        
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
        }
       
        return response()->json($response_data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:admin_user_types,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $userType = AdminUserType::find($request->data); 
            if($userType){
                    $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'User type'])]; 
            }else{
                $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
            }

        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }
}

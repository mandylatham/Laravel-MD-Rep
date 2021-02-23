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
use App\Permission;
use App\AdminPermission;
use App\AdminUserType;
use Illuminate\Contracts\Encryption\DecryptException;

class PermissionController extends Controller
{
    
    public function index()
    {
        $response_data["title"] = __("title.private.admin_type_list");
        return view("private.permission.userType")->with($response_data);
    }

    public function getPermissionList(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "user_type" => "required|exists:admin_user_types,id,deleted_at,NULL",
                ]);
        if(!$validator->fails()){
            $permissions = Permission::with(['assignedPermission' => function ($query) use ($request){
                                $query->where('user_type', $request->user_type);
                            }])->whereActive(1)->whereType(2)->get();
            $response_data = ["success" =>  1, "data" => $permissions];
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
        
        return response()->json($response_data);
    }

    //Blocked User
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "user_type" => "required|exists:admin_user_types,id,deleted_at,NULL",
                ]);
        if(!$validator->fails()){
            $userType = $request->user_type;
            AdminPermission::whereUserType($userType)->update(["active" => 0]);
            if($request->has("permission") && count($request->permission) > 0){
                foreach ($request->permission as $permission) {
                    AdminPermission::updateOrCreate(
                                ['user_type' => $userType, 'permission' => $permission],
                                ["active" => 1, 'created_by' => Auth::id(), 'updated_by' => Auth::id(), "updated_at" => now()]
                        );
                }
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Permission"])];
            }else{
                $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
            }
            
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }
}

<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use App\User;
use App\UserPost;
use App\PostComment;
use App\Gender;
use App\Exports\UsersExport;
use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{
    
    public function index()
    {
        $response_data["title"] = __("title.private.user_list");
        $response_data["genders"] = Gender::whereActive(1)->get();
        return view("private.user.list")->with($response_data);
    }

    public function userCount()
    {
        $response_data = User::count();
        return response()->json($response_data);
    }



    public function getList(Request $request)
    {
        $query = User::with("genderType:id,name");
        if($request->has("gender_type") && $request->gender_type != ""){
            $query->whereGender($request->gender_type);
        }
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', "{$request->get('start_date')}");
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', "{$request->get('end_date')}");
        }
       return Datatables::of($query->get())->make(true);
    }

    public function viewProfile($key, Request $request)
    {
        try {
            $userId = decrypt($key);
            $response_data["title"] = __("title.private.user_profile");
            $response_data["key"] = $key;
            $user = User::find($userId);
            if($user){
                $response_data["profile"] = User::withCount(["followerList", "followedList", "posts"])->whereId($user->id)->first();
                return view("private.user.profile")->with($response_data);
            }else{
                return redirect(route("private.users"));
            }
        } catch (DecryptException $e) {
            return redirect(route("private.users"));
        }
        
    }


    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "value" => "required|in:0,1",
                    "pk" => "required|exists:users,id",
                ]);
        if(!$validator->fails()){
            $user = User::find($request->pk);
            $user->active = $request->value;
            if($user->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "User Status"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }

    //User Delete
    public function destroy(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:users,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $user = User::find($request->data);    
            if($user->delete()){
                $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'User'])]; 
            }else{
                
                $response_data =  ['success' => 1, 'message' => __('site.server_error')]; 
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

    //EXPORT USER

    public function export(Request $request,$gender,$status,$from,$to)
    {     
        
        return Excel::download(new UsersExport($gender,$status,$from,$to), 'User-list-'.date("d-m-Y").'.csv'); 
    }

}

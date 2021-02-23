<?php

namespace App\Http\Controllers;

use App\Club;
use Validator;
use Illuminate\Http\Request;
use Auth;
use Yajra\Datatables\Datatables;
use App\AdminUser;
use Illuminate\Support\Facades\Hash;

class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response_data["title"] = __("title.private.club_list");
        return view("private.club.list")->with($response_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_request = $request->all();
        //Log::debug($new_request);
        $validator = Validator::make($new_request,
            [
              'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|not_exists:users,email,active,1,deleted_at,NULL',
              'phone'       => 'required|numeric|digits:'.limit("phone.max").'|not_exists:users,phone,active,1,deleted_at,NULL',
              'password'    => 'required|min:'.limit("password.min").'|max:'.limit("password.max").'|password'
            ]);

        if(!$validator->fails()){
            
            $clubRegister = new ClubRegister();
            $clubRegister->name           = $request->name;
            $clubRegister->email          = $request->email;
            $clubRegister->password       = bcrypt($request->password);
            $clubRegister->phone          = $request->phone;
            $clubRegister->active         = 1;
            $clubRegister->created_by     = 1;
            $clubRegister->updated_by     = 1;

            if($clubRegister->save()){
                $response_data = ["success" => 1,"message" => __("site.clubRegister")];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        //Log::debug($response_data);
 
        return response()->json($response_data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function show(Club $club)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function edit(Club $club)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Club $club)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function destroy(Club $club)
    {
        //
    }

    public function getList(Request $request)
    {
        $query = AdminUser::whereHas('userType', function ($query) {
                    $query->where('code', '=', 'club');
                });
        
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }
       return Datatables::of($query->get())->make(true);
    }

}

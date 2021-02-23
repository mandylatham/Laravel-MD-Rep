<?php

namespace App\Http\Controllers\PrivateArea;

use App\Club;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Event;
use App\EventType;
use Avatar;
use Yajra\Datatables\Datatables;
use App\AdminUser;
use App\AdminUserType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


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
              'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|not_exists:admin_users,email',
              'phone'       => 'required|numeric|digits:'.limit("phone.max").'|not_exists:admin_users,phone',
              'password'    => 'required|min:'.limit("password.min").'|max:'.limit("password.max").'|password',
               "status_type"    => "required|integer"
            ]);

        if(!$validator->fails()){
            if(!$this->registered($request))
            {
                $clubRegister = new AdminUser();
                $clubRegister->name           = $request->name;
                $clubRegister->email          = $request->email;
                $clubRegister->password       = Hash::make($request->password);
                $clubRegister->phone          = $request->phone;
                $clubRegister->tel_code       = "+1";
                $clubRegister->user_type      = AdminUserType::whereCode('club')->first()->id;
                $clubRegister->active         = $request->status_type;
                $clubRegister->created_by     = 1;
                $clubRegister->updated_by     = 1;

                $filePath = "images/profile/";
                $fileLocal =  "storage/".$filePath.uniqid().".png";
                
                // $file =  $filePath.uniqid().".png";
                // $file1 =  "storage/".$file;
                Avatar::create(strtoupper($request->name))->save($fileLocal, $quality = 90);
                $contents = Storage::disk("local")->get($filePath);
                $file = Storage::disk('s3')->put($filePath,$contents);

                //Avatar::create(strtoupper($request->name))->save($file, $quality = 90);
                $clubRegister->profile_pic    = Storage::url($file);
                $clubRegister->save();
                $insertedId = $clubRegister->id;
                $clubRegister->code           = hash('md5',"CLUB_000".$insertedId);
                if($clubRegister->save()){
                    $response_data = ["success" => 1,"message" => __("validation.create_success",['attr'=>'Club'])];
                }else{
                    $response_data = ["success" => 0, "message" => __("site.server_error")];
                } 
            }
            else
            {
                $response_data = ["success" => 0,"message" => __("validation.email_already",['attr'=>'Club'])];
            }
            
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
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
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'userId' => 'required|exists:admin_users,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $userId = $request->userId;
            $user = AdminUser::where('id', $userId)->first();
            if($user)
            {

                $response_data = ["success" => 1, "message" => __("validation.edit_success"), "record" => $user];
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
        $user = AdminUser::whereId($request->userId)->first();
        if($user){
            $userId = $user->id;
             $new_request = $request->all();
            //Log::debug($new_request);
            $validator = Validator::make($new_request,
                [
                  'name'        => 'required|min:'.limit("name.min").'|max:'.limit("name.max").'|valid_name',
                  'email'       => 'required|email|min:'.limit("email.min").'|max:'.limit("email.max").'|unique:admin_users,email,'.$userId.',id,deleted_at,NULL',
                  'phone'       => 'required|numeric|digits:'.limit("phone.max").'|unique:admin_users,phone,'.$userId.',id,deleted_at,NULL',
                  'password'    => 'nullable|min:'.limit("password.min").'|max:'.limit("password.max").'|password'
                ]);

            if(!$validator->fails()){
                
                $clubUpdate = AdminUser::whereId($request->userId)->first();
                $clubUpdate->name           = $request->name;
                $clubUpdate->email          = $request->email;
                if(!empty($request->password)){
                    $clubUpdate->password       = Hash::make($request->password);
                }
                $clubUpdate->phone          = $request->phone;
                $clubUpdate->updated_by     = 1;
                if($clubUpdate->save()){
                    $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'Club'])];
                }else{
                    $response_data = ["success" => 0, "message" => __("site.server_error")];
                } 
            
            }else{
                $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
            }
        }
        else
        {
            $response_data = ["success" => 0, "message" => __("site.server_error")];
        }
       
        //Log::debug($response_data);
 
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
              'data' => 'required|exists:admin_users,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $user = AdminUser::find($request->data); 
            if($user){
                if($user->delete()){
                    $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Club'])]; 
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

    public function getList(Request $request)
    {
        $query = AdminUser::whereHas('userType', function ($query) {
                    $query->where('code', '=', 'club');
                });
        
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', "{$request->get('start_date')}");
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', "{$request->get('end_date')}");
        }
       return Datatables::of($query->get()->sortByDesc('created_at'))->make(true);
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
                $response_data = ["success" => 1, "message" => __("validator.update_success", ["attr" => "Club Status"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }

    protected function registered(Request $request)
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


    public function viewProfile($key, Request $request)
    {
        try {
            $key = $key;
            $response_data["title"] = __("title.private.user_profile");
            $user = AdminUser::whereCode($key)->first();
            if($user){
                $response_data["profile"] = $user;
                $response_data["countEvent"] = Event::whereClubUser($user->id)->whereActive(1)->count();
                return view("private.club.profile")->with($response_data);
            }else{
                return redirect(route("private.club"));
            }
        } catch (DecryptException $e) {
            return redirect(route("private.club"));
        }
        
    }

    //Club user event list
    public function eventList($key, Request $request)
    {
        try {
            $key = $key;
            $response_data["title"] = __("title.private.event_list");
            $user = AdminUser::whereCode($key)->first();
            if($user){
                $response_data["name"] = $user->name;
                $response_data["code"] = $user->code;
                $response_data["eventTypes"] = EventType::whereActive(1)->get();
                return view("private.club.eventlist")->with($response_data);
            }else{
                return redirect(route("private.club"));
            }
        } catch (DecryptException $e) {
            return redirect(route("private.club"));
        }
    }

    public function getEventList(Request $request)
    {
        $query = Event::with(['eventType:id,name']);
        
        if($request->has("code") && $request->code != ""){
            $user = AdminUser::whereCode($request->code)->first();
            $query->whereClubUser($user->id);
        }

        if($request->has("event_type") && $request->event_type != ""){
            $query->whereEventType($request->event_type);
        }
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }

       return Datatables::of($query->get())->make(true);
    }

}

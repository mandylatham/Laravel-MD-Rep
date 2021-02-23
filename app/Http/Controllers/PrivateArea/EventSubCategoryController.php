<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\EventType;
use Yajra\Datatables\Datatables;
use Illuminate\Contracts\Encryption\DecryptException;

class EventSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response_data["title"] = __("title.private.event_sub_category_list");
        $response_data["eventTypes"] = EventType::whereParentId(0)->get();
        return view("private.permission.eventSubType")->with($response_data);
    }


     public function getEventSubCategoryList(Request $request)
    {
        $query = EventType::has("parentType")->where("parent_id", ">", 0);
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }
        if($request->has("type") && $request->type != ""){
            $query->whereParentId($request->type);
        }
        return Datatables::of($query->get())->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $new_request = $request->all();
        //Log::debug($new_request);
        $validator = Validator::make($new_request,
            [
                'name'          => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string',
                "event_type"    => "nullable|exists:event_types,id,active,1,parent_id,0,deleted_at,NULL",
            ]);

        if(!$validator->fails()){
            
            $eventSub = new EventType();
            $eventSub->name           = $request->name;
            if(!empty($request->event_type))
            {
                $eventSub->parent_id      = $request->event_type;
            }
            $eventSub->active         = 1;
            $eventSub->created_by     = Auth::id();
            $eventSub->updated_by     = Auth::id();
            if($eventSub->save()){
                $response_data = ["success" => 1,"message" => __("validation.create_success",['attr'=>'Event Sub Category'])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()];
        }
    
        return response()->json($response_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EventSubCategory  $eventSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show(EventSubCategory $eventSubCategory)
    {
        //
    }


    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "value" => "required|in:0,1",
                    "pk" => "required|exists:event_types,id",
                ]);
        if(!$validator->fails()){
            $eventSubCategory = EventType::find($request->pk);
            $eventSubCategory->active = $request->value;
            if($eventSubCategory->save()){
                $response_data = ["success" => 1, "message" => __("validator.update_success", ["attr" => "Event Sub Category Status"])];
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
     * @param  \App\EventSubCategory  $eventSubCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:event_types,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $eventId = $request->data;
            $event = EventType::where('id', $eventId)->first();
            if($event)
            {

                $response_data = ["success" => 1, "message" => __("validation.edit_success"), "record" => $event];
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
     * @param  \App\EventSubCategory  $eventSubCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $typeId = $request->data;
        $new_request = $request->all();
        $validator = Validator::make($new_request,
            [
                'data' => 'required|exists:event_types,id,deleted_at,NULL',
                'name' => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string|unique:event_types,name,'.$typeId.',id,deleted_at,NULL',
                 "event_type"    => "nullable|exists:event_types,id,active,1,parent_id,0,deleted_at,NULL",
            ]);

        if(!$validator->fails()){
            
           $eventSubCategory = EventType::find($typeId);
           $eventSubCategory->name           = $request->name;
            if(!empty($request->event_type))
            {
                $eventSubCategory->parent_id      = $request->event_type;
            }
           $eventSubCategory->updated_by     = Auth::id();

            if($eventSubCategory->save()){
                $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'Event Sub Category'])];
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
     * @param  \App\EventSubCategory  $eventSubCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:event_types,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $event = EventType::find($request->data); 
            if($event->delete()){
                $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Event Sub Category'])]; 
            }else{
                
                $response_data =  ['success' => 1, 'message' => __('site.server_error')]; 
            }

        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }
}

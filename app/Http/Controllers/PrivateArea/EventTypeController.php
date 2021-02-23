<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EventType;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Contracts\Encryption\DecryptException;

class EventTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response_data["title"] = __("title.private.event_type_list");
        $response_data["eventTypes"] = EventType::whereActive(1)->whereParentId(0)->get();
        return view("private.permission.eventType")->with($response_data);
    }


    public function getEventTypeList(Request $request)
    {
        $query = EventType::whereNull('deleted_at')->whereParentId(0);
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
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
            ]);

        if(!$validator->fails()){
       
            $eventType = new EventType();
            $eventType->name           = $request->name;
            $eventType->parent_id      = 0;
            $eventType->active         = 1;
            $eventType->created_by     = Auth::id();
            $eventType->updated_by     = Auth::id();
            if($eventType->save()){
                $response_data = ["success" => 1,"message" => __("validation.create_success",['attr'=>'Event Type'])];
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
     * @param  \App\EventType  $eventType
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EventType  $eventType
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
            $typeId = $request->data;
            $eventType = EventType::where('id', $typeId)->first();
            if($eventType)
            {
                $response_data = ["success" => 1, "data" => $eventType];
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
     * @param  \App\EventType  $eventType
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
            ]);

        if(!$validator->fails()){
            
           $eventType = EventType::find($typeId);
           $eventType->name           = $request->name;
           $eventType->updated_by     = Auth::id();
           
            if($eventType->save()){
                $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'Event type'])];
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
     * @param  \App\EventType  $eventType
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
            $eventType = EventType::find($request->data); 
            if($eventType->delete()){
                    $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Event type'])]; 
            }else{
                $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
            }

        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }


    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "value" => "required|in:0,1",
                    "pk" => "required|exists:event_types,id",
                ]);
        if(!$validator->fails()){
            $eventType = EventType::find($request->pk);
            $eventType->active = $request->value;
            if($eventType->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Status"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }

}

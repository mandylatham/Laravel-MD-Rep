<?php

namespace App\Http\Controllers\PrivateArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\StrickersType;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Contracts\Encryption\DecryptException;
class StickersTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response_data["title"] = __("title.private.sticker_type_list");
        $response_data["stickerTypes"] = StrickersType::whereActive(1)->get();
        return view("private.permission.stickerType")->with($response_data);
    }


    public function getStickerTypeList(Request $request)
    {
        $query = StrickersType::whereNull('deleted_at');
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
                'name'          => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string|not_exists:strickers_types,name',
                 
            ]);

        if(!$validator->fails()){
       
            $stickerType = new StrickersType();
            $stickerType->name           = $request->name;
            $stickerType->active         = 1;
            $stickerType->created_by     = Auth::id();
            $stickerType->updated_by     = Auth::id();
            if($stickerType->save()){
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\stickerType  $stickerType
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:strickers_types,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $typeId = $request->data;
            $stickerType = StrickersType::where('id', $typeId)->first();
            if($stickerType)
            {
                $response_data = ["success" => 1, "data" => $stickerType];
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
     * @param  \App\stickerType  $stickerType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $typeId = $request->data;
        $new_request = $request->all();
        $validator = Validator::make($new_request,
            [
                'data' => 'required|exists:strickers_types,id,deleted_at,NULL',
                'name' => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string|unique:strickers_types,name,'.$typeId.',id,deleted_at,NULL',
            ]);

        if(!$validator->fails()){
            
           $stickerType = StrickersType::find($typeId);
           $stickerType->name           = $request->name;
           $stickerType->updated_by     = Auth::id();
           if(!empty($request->event_type))
            {
                $stickerType->parent_id      = $request->event_type;
            }
            if($stickerType->save()){
                $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'Sticker type'])];
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
     * @param  \App\stickerType  $stickerType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:strickers_types,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $stickerType = StrickersType::find($request->data); 
            if($stickerType->delete()){
                    $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Sticker type'])]; 
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
                    "pk" => "required|exists:strickers_types,id",
                ]);
        if(!$validator->fails()){
            $stickerType = StrickersType::find($request->pk);
            $stickerType->active = $request->value;
            if($stickerType->save()){
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

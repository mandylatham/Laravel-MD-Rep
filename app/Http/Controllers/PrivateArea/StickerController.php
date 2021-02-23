<?php

namespace App\Http\Controllers\PrivateArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sticker;
use App\StrickersType;
use Validator;
use App\User;
use Yajra\Datatables\Datatables;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;


class StickerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response_data["title"] = __("title.private.stickers_list");
        $response_data["stickerTypes"] = StrickersType::whereActive(1)->get();
        return view("private.sticker.list")->with($response_data);
    }


    public function getStickerList(Request $request)
    {
        $query = Sticker::with(['stickerType:id,name']);

        if($request->has("sticker_type") && $request->sticker_type != ""){
            $query->whereType($request->sticker_type);
        }
        if($request->has("status") && $request->status != ""){
            $query->whereActive($request->status);
        }
       return Datatables::of($query->get())->make(true);
    }

    /**
     * Returns Event Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStickers(Request $request)
    {
        $limit      = config("site.papgination.stickers");
        $stickers   = Sticker::whereActive(1)->paginate($limit);

        $response_data = ["success" => 1, "data" => $stickers];
        return response()->json($response_data);
    }


    /**
     * Returns Event Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
      //dd($request->all());
        $validator = Validator::make($request->all(),
            [
              'name'              => 'nullable|max:'.limit("name.max").'|string',
              'uploadImg2.*'       => 'required|image|mimes:'.limit("sticker_image.format").'|max:'.limit("sticker_image.max"),
              'sticker_type' => 'required|numeric|exists:strickers_types,id,deleted_at,NULL',
            ]);
            
        if(!$validator->fails()){
            $user = auth()->user();
            $attachments = [];
            if($request->uploadImg2 > 0)
            {
              $i = 0;
              foreach($request->uploadImg2 as $file) 
              {
                $attachment     = []; 
                $attachment["name"]    = ucwords($request->name); 
                $filePath = "stricker/images";
                $path = Storage::url($request->file("uploadImg2.".$i++)->store($filePath));
                $attachment["path"]    = $path; 
                $attachment["type"]    = $request->sticker_type; 
                $attachment["active"]  = 1; 
                $attachments[] = $attachment;
              }
            }
            
            if(count($attachments) > 0){
                $attchementResult = Sticker::insert($attachments);
                $response_data = ["success" => 1, "message" => __("validation.create_success", ["attr" => "Sticker"])];
            }else{
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\stickerType  $stickerType
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data' => 'required|exists:stickers,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $typeId = $request->data;
            $sticker = Sticker::where('id', $typeId)->first();
            if($sticker)
            {
                $response_data = ["success" => 1, "data" => $sticker];
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
                'data' => 'required|exists:stickers,id,deleted_at,NULL',
                'name' => 'required|min:'.limit("user_type.min").'|max:'.limit("user_type.max").'|string',
                'sticker_type' => 'required|numeric|exists:strickers_types,id,deleted_at,NULL',
                'uploadImg1'=> 'nullable|image|mimes:'.limit("sticker_image.format").'|max:'.limit("sticker_image.max")
            ]);

        if(!$validator->fails()){
           $user = auth()->user(); 
           $sticker = Sticker::find($typeId);
           $sticker->name           = $request->name;
           if(!empty($request->file('uploadImg1')))
            {
                
                $filePath = "stricker/images";
                $image = Storage::url($request->uploadImg1->store($filePath));
                $sticker->path = $image;
            }
           //$sticker->updated_by     = $user->id;
           if(!empty($request->sticker_type))
            {
                $sticker->type      = $request->sticker_type;
            }
            if($sticker->save()){
                $response_data = ["success" => 1,"message" => __("validation.update_success",['attr'=>'Sticker'])];
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
              'data' => 'required|exists:stickers,id,deleted_at,NULL',
            ]);
            
        if (!$validator->fails()) 
        { 
            $sticker = Sticker::find($request->data); 
            if($sticker->delete()){
                    $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Sticker'])]; 
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
                    "pk" => "required|exists:stickers,id",
                ]);
        if(!$validator->fails()){
            $sticker = Sticker::find($request->pk);
            $sticker->active = $request->value;
            if($sticker->save()){
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

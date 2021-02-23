<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sticker;
use App\StrickersType;
use Validator;
use App\User;
use App\UserDashboard;
use App\SocialToken;
use App\DeviceToken;
use App\PasswordReset;
use App\Events\LoginEvent;
use Illuminate\Support\Facades\Hash;
use Avatar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use DB;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;


class DashboardController extends Controller
{
    /**
     * Returns Event Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStickers(Request $request)
    {
        $limit      = config("site.papgination.stickers");
        /*$stickers   = Sticker::whereActive(1)->paginate($limit);*/
        $stickers   = Sticker::whereActive(1)->get();
        $response_data = ["success" => 1, "data" => $stickers];
        return response()->json($response_data);
    }

    public function stickersType(Request $request)
    {
        $stickersType   = StrickersType::whereActive(1)->get();
        $response_data  = ["success" => 1, "data" => $stickersType];
        return response()->json($response_data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'name'             => 'required|max:'.limit("post_text.max"),
              'type'             => 'required|numeric',
              'image'            => 'required|mimes:'.limit("file.format")
             
            ]);
            
        if(!$validator->fails()){
 
            $stricker               = new Sticker();
            $stricker->name         = $request->name;       
            $stricker->type         = $request->type; 
            if($request->hasFile('image'))
            { 
                $filePath = "stricker/images";
                $image = Storage::url($request->image->store($filePath));
            }
            $stricker->path = $image;
            $stricker->save();

            $response_data = ["success" => 1, "message" => __("validation.create_success", ["attr" => "stricker"])];
                        
        }
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }

     public function createStrickerType(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'name'             => 'required|max:'.limit("post_text.max"),
            ]);
            
        if(!$validator->fails()){
 
            $strickerType               = new StrickersType();
            $strickerType->name         = $request->name;
            $strickerType->created_by   = 47;
            $strickerType->updated_by   = 47;       
            $strickerType->save();
            $response_data = ["success" => 1, "message" => __("validation.create_success", ["attr" => "strickerType"])];        
        }
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }

    public function getDashboardImage(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'user_data'       => 'nullable|exists:users,id,active,1,deleted_at,NULL'
            ]);

        if(!$validator->fails()){

            if($request->has("user_data")){
                $user = User::find($request->user_data);
                $userId = $request->user_data;
            }else{
                $user = auth()->user();
                $userId = $user->id;
            }
            $dashboard = UserDashboard::whereUserId($userId)->first();
            if($dashboard){
                $imageUrl = $dashboard->background;
            }else{
                $imageUrl = "";

            }
            $response_data = ["success" => 1, "data" => $imageUrl,"profile" => $user->profile_pic];        
        }
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }


    public function getDashboardLayers(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        $dashboard = UserDashboard::whereUserId($userId)->first();
        if($dashboard){
            if(isJson($dashboard->layer)){
                $data = json_decode($dashboard->layer, true);
                
                $croppedImage = convertBase64($data["layers"]["croppedImage"]);
                $stickers = $data["layers"]["stickers"];
                $newStickers = [];
                if(count($stickers) > 0){
                    foreach ($stickers as $sticker) {
                        $sticker["image"] = convertBase64($sticker["image"]);
                        $newStickers[] = $sticker;
                    }
                }
                $data["layers"]["stickers"] = $newStickers;
                $data["layers"]["croppedImage"] = $croppedImage;
                $imageUrl = $data;
            }else{
                $imageUrl["layers"] = [];
            }
            
        }else{
            $imageUrl = ["layers"];
        }
        $sticker = StrickersType::with("stickers:id,type,path")->select("id", "name")->whereActive(1)->get();
        $imageUrl["stickers"] = $sticker;
        $response_data = ["success" => 1, "data" => $imageUrl];        
        
        return response()->json($response_data);
    }


    public function updateDashboardLayers(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'data'       => 'required|json'
            ]);
        if(!$validator->fails()){
            $user = auth()->user();
            $userId = $user->id;

            $data = json_decode($request->data, false);
            //dd($data);
            $dashboardImage = $data->dashboardImage;
            $croppedImage = $data->layers->croppedImage;
            $stickers = $data->layers->stickers;
            $newStickers = [];
            if(count($stickers) > 0){

                foreach ($stickers as $sticker) {
                    $stickerImg = $sticker->image;
                    $stickerFilepath = "storage/dashboard/stickers/".md5(time().rand(1111,9999)).".png";
                    $path = Image::make($stickerImg)->save($stickerFilepath);
                    $sticker->image = $stickerFilepath;
                    $newStickers[] = $sticker;
                }
            }

            $dashboardPath = "storage/dashboard/".md5(time().rand(1111,9999)).".png";
            $path = Image::make($dashboardImage)->save($dashboardPath);
            $croppedPath = "storage/dashboard/".md5(time().rand(1111,9999)).".png";
            $path = Image::make($croppedImage)->save($croppedPath);
            $data->layers->croppedImage = $croppedPath;
            $data->layers->stickers = $newStickers;
            unset($data->dashboardImage);

            $dashboard = UserDashboard::whereUserId($userId)->first();
            if(!$dashboard){
                $dashboard = new UserDashboard();
            }
            $dashboard->user_id = $userId;
            $dashboard->background = $dashboardPath;
            $dashboard->layer = json_encode($data);
            $dashboard->created_by = $userId;
            $dashboard->updated_by = $userId;
            $dashboard->save();

            
            if($dashboard->save()){
                $response_data = ["success" => 1, "message" =>  __("validation.create_success", ["attr" => "Dashboard"])];    
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];    
            }   
        }else{
            $response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "User"])];
        }
        return response()->json($response_data);
           
    }



    /**
     * Returns Event Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function interiorSticker(Request $request)
    {
       $validator = Validator::make($request->all(),
            [
              'type' => 'required|numeric'
            ]);
        
        if(!$validator->fails()){
 			$stricker = Sticker::whereType($request->type)->whereActive(1)->get();
            $response_data  = ["success" => 1, "data" => $stricker];
       }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }
    
    /**
     * Returns Event Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStickersa(Request $request)
    {
        /*Log::debug($request->all());
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'lat'         => 'nullable|numeric',
            ]);

        if(!$validator->fails()){

            

            $response_data = ["success" => 1, "data" => ["events" => $data, "event_types" => $eventTypes]];
            
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        //Log::debug($response_data);
        return response()->json($response_data);*/
    }





}

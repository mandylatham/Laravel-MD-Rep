<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\UserNotification;

class NotificationController extends Controller
{
    /**
     * Get User Notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              //'date'       => 'required|date',
            ]);
        if(!$validator->fails()){
            $pagination = config("pagination.notification");
            $user = auth()->user();
            $notifications =  UserNotification::has('event')
                                    ->whereToUser($user->id)
                                    ->whereNull("read_at")
                                    ->orderBy("created_at", "desc")
                                    ->paginate($pagination);
            $response_data = ["success" => 1,  "data" => $notifications];
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }

    /**
     * Update User Notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotification(Request $request)
    {
    	$user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'data'       => 'required|exists:user_notifications,id,to_user,'.$user->id.',deleted_at,NULL',
            ]);
        if(!$validator->fails()){
            $notification =  UserNotification::whereId($request->data)
                                    ->whereNull("read_at")
                                    ->update(["read_at" => now()]);
			$response_data = ["success" => 1,  "message" => __("validation.update_success", ["attr" => "Notification"])];
            /*if($notification){
            }else{
            	$response_data = ["success" => 0,  "message" => __("site.server_error")];
            }*/
        }else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        return response()->json($response_data);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Follower;
use App\User;
use App\UserBlock;
use App\EventBook;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FollowController extends Controller
{
    /**
     * Returns Follower List
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersList(Request $request)
    {
    	  //Log::info($request->all());

        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'name'        => 'nullable|string',
              'limit'       => 'nullable|numeric',
            ]);
        if(!$validator->fails()){

           
            $query = UserBlock::select(["block_user"])->whereStatus(1)->whereUserId(Auth::id())->get();
            $users = $query->pluck("block_user")->toArray();
            $users[] = $user->id;
            $query = User::select("id", "name", "gender","profile_pic")->whereNotIn('id',$users);
            if($request->filled("name")){
                $query->search($request->name);
               
            }else{
            	$userFilter = [];
            	$followerUser = [];
            	$eventUser = [];
                $attendEvent =EventBook::whereUserId(Auth::id())->get(); 
                if($attendEvent)
                {
                    $eventBooked =$attendEvent->pluck('event_id');
                    $getUser = EventBook::whereIn('event_id',$eventBooked)->pluck('user_id')->unique()->values();
                    $eventUser =$getUser->toArray();
                }
                $follower = Follower::select("follower_id")->whereFollowedId($user->id)->get();
                if($follower){
                	$followerUser = $follower->pluck("follower_id")->toArray();
                }
                $userFilter = array_unique(array_merge($followerUser, $eventUser));
                
                $query = $query->whereIn('id', $userFilter);
                $query->orderBy('name', 'asc');
            }

            $query->whereActive(1);
            if($request->limit > 0){
                $data = $query->paginate($request->limit);
            }else{
                $data = $query->get();
            }
            $response_data = ["success" => 1, "data" => $data];
        }
        else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        
        return response()->json($response_data);
    }


       /**
     * Create Follow or Unfollow
     *
     * @param  [string] email
     * @return [string] message
     */
    public function follow(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), 
            [
                'status'     => 'required|numeric|in:0,1',
                'user_id'    => 'required|numeric|not_in:'.$user->id.'|user_block',
            ]);

        if(!$validator->fails()){
            
            $deleted_at = NULL;
            $follow = "User followed";

            if($request->status == 0)
            {
                $follow = "User unfollowed";
                $deleted_at = now();
            }

            $data = Follower::updateOrInsert(
                        ['follower_id' => $user->id, 'followed_id' => $request->user_id],
                        ['created_by' => $user->id, 'updated_by' => $user->id, "deleted_at" => $deleted_at]
                    );

            if($data)
             {
                 $response_data =  ['success' => 1, 'message' => __('validation.unfollow_success',['attr'=> $follow])];
             }
             else
             {
                 $response_data = ["success" => 0, "message" => __("site.server_error")];
             }

        }else{
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }

      /**
     * Follower List
     *
     * @param  [string] email
     * @return [string] message
     */
    public function followerList(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
              'limit'        => 'nullable|numeric',
              'status'       => 'required|numeric|in:1,2',
            ]);
        if(!$validator->fails()){

            if($request->status == 1)
            {
                $follower = Follower::select("followed_id")->whereFollowerId($user->id)->get();
                $query = User::whereIn("id",$follower);
                if($request->limit > 0){
                    $data = $query->paginate($request->limit);
                }else{
                    $data = $query->get();
                }

                $data = $user->followerList;
            }
            else
            {
                $follower = Follower::select("follower_id")->whereFollowedId($user->id)->get();
                $query = User::whereIn("id",$follower);
                if($request->limit > 0){
                    $data = $query->paginate($request->limit);
                }else{
                    $data = $query->get();
                }

                $data = $user->followedList;
            }

            
            //dd($data->toArray());
            $response_data = ["success" => 1, "data" => $data];
            
        }
        else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        
        return response()->json($response_data);
    }


}

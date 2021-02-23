<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\UserPost;
use App\Event;
use App\PostComment;
use App\ChatGroup;
use App\ChatMessage;
use App\UserBlock;
use Auth;

class CustomValidationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });

        Validator::extend('valid_name', function($attribute, $value)
        {
            return preg_match('/^[\pL\s\.]+$/u', $value);
        });

        Validator::extend('alphanumericspaces', function($attribute, $value)
        {
            return preg_match('/^[\pL0-9\s.]+$/u', $value);
        });

        Validator::extend('not_exists', function ($attribute, $value, $parameters, $validator) {
            
            if(count($parameters) < 4 && count($parameters)%2 != 0){
                return false;
            }
            
            return !$validator->validateExists($attribute, $value, $parameters);
        });

        Validator::extend('password', function ($attribute, $value, $parameters, $validator) {
            $regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,40}$/";
            return preg_match($regex, $value);
        });

        Validator::extend('latitude', function ($attribute, $value, $parameters, $validator) {
            $regex = "/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/";
            return preg_match($regex, $value);
        });

        Validator::extend('longitude', function ($attribute, $value, $parameters, $validator) {
            $regex = "/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/";
            return preg_match($regex, $value);
        });


        Validator::extend('event_date', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $success = true;
            try {
                if(isset($data["start_date"]) && !empty($data["start_date"]) && isset($data["end_date"]) && !empty($data["end_date"])){
                    $startDate = Carbon::parse($data["start_date"]);
                    $endDate = Carbon::parse($data["end_date"]);
                    $today = now();
                    if($endDate->gt($startDate) && $startDate->gte($today)){
                        /*if(isset($data["end_time"]) && isset($data["start_time"])){
                            $startTime = Carbon::parse($data["start_date"]." ".$data["start_time"]);
                            $endTime = Carbon::parse($data["end_date"]." ".$data["end_time"]);
                            if($endDate->lte($startDate) && $startTime->gte($today)){
                                $success = false;
                            }
                        }*/
                        $success = true;
                    }else{
                        $success = false;
                    }
                }
            } catch (Exception $e) {
                $success = false;
            }
            
            return $success;
        });

        Validator::extend('limit_file', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $files = $data[$parameters[0]];

            return count($files) <= $parameters[1];
        });

        Validator::extend('my_group', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $user = auth()->user();
            $group = ChatGroup::whereChatName($value)->first();
            $response = false;
            if($group){
                if($group->from_user == $user->id || $group->to_user == $user->id){
                    $response = true;
                }
            }
            return $response;
        });
        
        //Check Post or current user only delete the comment
        Validator::extend('checkpostuser', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $comment_id = $data["comment_data"];
            $comment = PostComment::whereId($comment_id)->first();
            $user = auth()->user();
            if($comment){
                $postuser = UserPost::whereId($comment->post_id)->first();
                if($user->id == $comment->user_id || $user->id == $postuser->user_id)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            
        });


        //Check Event Date less than equal end date in event table
        validator::extend('event_bookdate',function($attribute,$value,$parameters,$validator)
        {
            $data = $validator->getData();
            $event_id = $data["event_id"];
            $event = Event::whereId($event_id)->first();
            if($event)
            {
                $bookingDate = Carbon::parse($event->end_date);
                $currentDate = now();
                //dd(array($bookingDate,$currentDate));
                if($currentDate->lt($bookingDate)){
                    return true;
                }
            }
            return false;
            
        });

        //Check Event Date less than equal end date in event table
        validator::extend('check_chat_block',function($attribute,$value,$parameters,$validator)
        {
            $data = $value;
            $group = ChatGroup::whereChatName($value)->first();
            $userBlock = UserBlock::where(function($q) use ($group) {
                      $q->where([['user_id', $group->from_user], ["block_user",$group->to_user]])
                        ->orWhere([['user_id', $group->to_user], ["block_user",$group->from_user]]);
                  })->whereStatus(1)->count();
            if($userBlock > 0){
                return false;
            }
            return true;
            
        });

        //Check Event Date less than equal end date in event table
        validator::extend('user_block',function($attribute,$value,$parameters,$validator)
        {
            $to_user    = $value;
            $user       = auth()->user();
            $from_user  = $user->id;

            $userBlock = UserBlock::where(function($q) use ($from_user, $to_user) {
                      $q->where([['user_id', $from_user], ["block_user",$to_user]])
                        ->orWhere([['user_id', $to_user], ["block_user",$from_user]]);
                  })->whereStatus(1)->count();
            if($userBlock > 0){
                return false;
            }
            return true;
            
        });

        Validator::extend('my_chat', function($attribute, $value, $parameters, $validator) {
            $response = false;
            $data = $validator->getData();
            $user = auth()->user();
            $group = ChatGroup::whereChatName($data["chat_group"])->first();
            if($group){
                $message = ChatMessage::whereChatId($group->id)->where(function($q)  use($user) {
                      $q->where([['from_user', $user->id]])
                        ->orWhere([['to_user', $user->id]]);
                  })->first();
                if($message){
                    $response = true;
                }
            }
            
            return $response;
        });
        


    }
}

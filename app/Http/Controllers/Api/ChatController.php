<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\SocialToken;
use App\DeviceToken;
use App\PasswordReset;
use App\Events\LoginEvent;
use Illuminate\Support\Facades\Hash;
use Avatar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Events\NewChatEvent;
use App\Event;
use App\EventType;
use App\EventBook;
use App\UserNotification;
use App\ChatGroup;
use App\ChatMessage;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use DB;

class ChatController extends Controller
{
    
    public function getChats(Request $request)
    {
        $user = auth()->user();
        
        $chats = ChatGroup::with(["fromUser:id,name,gender,profile_pic", "toUser:id,name,gender,profile_pic", "lastChat"])->withCount(["unread"=> function ($query) {
                        $query->where('to_user', '=', auth()->user()->id);
                }])/*->where("from_user", $user->id)->orWhere("to_user", $user->id)*/->where(function($q)  use($user) {
                      $q->where([['from_user', $user->id], ["sender_deleted_at", "=", NULL]])
                        ->orWhere([['to_user', $user->id], ["receiver_deleted_at", "=", NULL]]);
                  })->whereStatus(1)->get();
        if($chats){
            $chats = $chats->toArray();
            $chats = collect($chats)->sortByDesc("last_chat.created_at")->values()->toArray();
        }
        $response_data = ["success" => 1, "data" => $chats];
        return response()->json($response_data);
    }

    public function message(Request $request)
    {
        Log::debug($request);
        $user = auth()->user();
        $validator = Validator::make($request->all(),
                    [
                        'chat_id'      => 'nullable|exists:chat_groups,chat_name,status,1,deleted_at,NULL|my_group|check_chat_block',
                        'message'      => 'nullable|string|min:'.limit("chat_message.min").'|max:'.limit("chat_message.max"),
                        'file'         => 'required_without:message|image|mimes:'.limit("chat_image.format"),
                        'to_user'      => 'required|exists:users,id,active,1,blocked_at,NULL,deleted_at,NULL|user_block|not_in:'.$user->id
                    ]);

        if(!$validator->fails()){
            $message = "";
            $type = config("site.chat_type.text");
            if($request->filled("message")){
                $message = $request->message;
            }else{
                $filePath   = "chat/images";
                $message    = Storage::url($request->file->store($filePath));
                $type = config("site.chat_type.image");
            }
            $toUser = $request->to_user;
            $group = 0;

            if($request->filled("chat_id")){
                $chatGroup = ChatGroup::whereChatName($request->chat_id)->first();
                if($chatGroup){
                    
                    if(($chatGroup->from_user == $user->id && $chatGroup->to_user == $toUser) || ($chatGroup->from_user == $toUser && $chatGroup->to_user == $user->id)){
                        $group = 1;
                    }
                }
            }else{
            	$chatGroup = ChatGroup::where([["from_user" ,"=", $user->id], ["to_user", "=", $toUser]])->orWhere([["from_user", "=", $toUser], ["to_user", "=", $user->id]])->first();
                if($chatGroup){
                    $group = 1;
                }
            }

            if($group == 0){
                $chatGroup = new ChatGroup();
                $chatGroup->chat_name = "fun_private_".$user->id."_".$toUser;
                $chatGroup->from_user = $user->id;
                $chatGroup->to_user = $toUser;
                $chatGroup->status = 1;
                $chatGroup->save();
            }

            /*if($chatGroup->from_user == $user->id)
            {*/
                $chatGroup->sender_deleted_at = NULL;
                $chatGroup->receiver_deleted_at = NULL;
            /*}
            else
            {
            }*/
            $chatGroup->save();

            $chatMessage = new ChatMessage();
            $chatMessage->chat_id = $chatGroup->id;
            $chatMessage->from_user = $user->id;
            $chatMessage->to_user = $toUser;
            $chatMessage->message = $message;
            $chatMessage->type = $type;
            $chatMessage->status = 1;
            $chatMessage->is_read = 0;
            $chatGroupName = $chatGroup->chat_name;
            if($chatMessage->save()){
                $chatMessage->load('toUser:id,gender,name,profile_pic');
                event(new NewChatEvent($toUser, $chatMessage, $chatGroup));
                $response_data = ["success" => 1, "data" => ["group" => $chatGroupName, "chat" => $chatMessage], "message" => __("validation.sent_success", ["attr" => "Message"])];
            }else{
               $response_data = ["success" => 0, "message" => __("site.server_error")];
            }

           
       }else{
           $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
       }
       return response()->json($response_data);
    }

    public function getChatMessage(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
                           [
                               'chat_id'      => 'nullable|exists:chat_groups,chat_name,status,1,deleted_at,NULL|my_group',
                               'to_user'      => 'required_without:chat_id|exists:users,id,active,1,blocked_at,NULL,deleted_at,NULL|not_in:'.$user->id,
                               'last_chat'      => 'nullable|integer'
                           ]);
        if(!$validator->fails()){

        	if($request->filled("chat_id")){
	            $chatGroup  = ChatGroup::whereChatName($request->chat_id)->whereStatus(1)->first();
        	}else{
        		$toUser = $request->to_user;
				$chatGroup = ChatGroup::where([["from_user" ,"=", $user->id], ["to_user", "=", $toUser]])->orWhere([["from_user", "=", $toUser], ["to_user", "=", $user->id]])->first();		
        	}

        	if(!$chatGroup){
                $chatGroup = new ChatGroup();
                $chatGroup->chat_name = "fun_private_".$user->id."_".$toUser;
                $chatGroup->from_user = $user->id;
                $chatGroup->to_user = $toUser;
                $chatGroup->status = 1;
                $chatGroup->save();
            }

            if($chatGroup->from_user == $user->id)
            {
                $chatGroup->sender_deleted_at = NULL;
            }
            else
            {
                $chatGroup->receiver_deleted_at = NULL;
            }
            $chatGroup->save();

        	ChatMessage::whereChatId($chatGroup->id)->whereToUser($user->id)->update(["is_read" => 1]);
            $chatGroupName = $chatGroup->chat_name;

            $customData = collect(["group_name" => $chatGroupName]);
			

            if($request->filled("last_chat") && !empty($request->last_chat)){
	            $chats      = ChatMessage::whereChatId($chatGroup->id)->orderBy("created_at", "desc")->where(function($q)  use($user) {
                      $q->where([['from_user', $user->id], ["sender_deleted_at", "=", NULL]])
                        ->orWhere([['to_user', $user->id], ["receiver_deleted_at", "=", NULL]]);
                  })->where("id", ">", $request->last_chat)->get();
            }else{
	            $limit      = config("site.pagination.chats");
	            $chats      = ChatMessage::whereChatId($chatGroup->id)->orderBy("created_at", "desc")->where(function($q)  use($user) {
                      $q->where([['from_user', $user->id], ["sender_deleted_at", "=", NULL]])
                        ->orWhere([['to_user', $user->id], ["receiver_deleted_at", "=", NULL]]);
                    })
                    ->get();
                    //->paginate($limit);
            }
            //$data = $customData->merge($chats);
            $data["chats"]      = $chats;
            $data["group_name"] = $chatGroupName;
            
            $response_data = ["success" => 1, "data" => $data];
        }else{
            $response_data = ["success" => 0, "message" => __("validation.refresh_page")];
        }
        return response()->json($response_data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
        [
          'chat_group'      => 'required|exists:chat_groups,chat_name,status,1,deleted_at,NULL|my_group',
          'chat_id'         => 'required_with:chat_group|my_chat',
        ]);
            
        if (!$validator->fails()) 
        { 
            $message = ChatMessage::find($request->chat_id);
            if($message->from_user == $user->id)
            {
              $message->sender_deleted_at = now();
            }
            else
            {
              $message->receiver_deleted_at = now();
            }

            if($message->save()){
                $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Message'])]; 
            }else{
                $response_data =  ['success' => 0, 'message' => __('site.server_error')]; 
            }
        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function clearChat(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
        [
          'chat_group'      => 'required|exists:chat_groups,chat_name,status,1,deleted_at,NULL|my_group'
        ]);
            
        if (!$validator->fails()) 
        { 
            $chatGroup = ChatGroup::whereChatName($request->chat_group)->first();

            $fromChat = ChatMessage::whereChatId($chatGroup->id)->where([['from_user', $user->id]])->update(["sender_deleted_at" => now()]);
            $toChat = ChatMessage::whereChatId($chatGroup->id)->where([['to_user', $user->id]])->update(["receiver_deleted_at" => now()]);

            if($fromChat && $toChat){
                $response_data =  ['success' => 1, 'message' => __('validation.cleared_success',['attr'=>'Chat history'])]; 
            }else{
                $response_data =  ['success' => 0, 'message' => __('site.server_error')]; 
            }
        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function removeGroup(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
        [
          'chat_group'      => 'required|exists:chat_groups,chat_name,status,1,deleted_at,NULL|my_group'
        ]);
            
        if (!$validator->fails()) 
        { 
            $chatGroup = ChatGroup::whereChatName($request->chat_group)->first();
        	$fromChat = ChatMessage::whereChatId($chatGroup->id)->where([['from_user', $user->id]])->update(["sender_deleted_at" => now()]);
            $toChat = ChatMessage::whereChatId($chatGroup->id)->where([['to_user', $user->id]])->update(["receiver_deleted_at" => now()]);

            if($chatGroup->from_user == $user->id)
            {
              	$chatGroup->sender_deleted_at = now();
            }
            else
            {
              	$chatGroup->receiver_deleted_at = now();
            }

            if($chatGroup->save()){
                $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Chat group'])]; 
            }else{
                $response_data =  ['success' => 0, 'message' => __('site.server_error')]; 
            }
        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }


 

}

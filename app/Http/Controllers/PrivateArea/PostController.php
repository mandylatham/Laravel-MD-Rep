<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Yajra\Datatables\Datatables;
use App\User;
use App\UserPost;
use App\PostComment;
use App\Gender;
use Illuminate\Contracts\Encryption\DecryptException;

class PostController extends Controller
{
    //Blocked User
    public function updateBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "value" => "required|in:0,1",
                    "pk" => "required|exists:users,id",
                ]);
        //dd($request->all());
        if(!$validator->fails()){
            $user = User::find($request->pk);
            if($request->value == 1)
            {
                $user->blocked_at = now();
            }
            elseif($request->value==0)
            {
                $user->blocked_at =NULL;
            }
            
            if($user->save()){
                $userTokens = $user->tokens;
                foreach($userTokens as $token) {
                    $token->revoke();   
                }
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "User Blocked"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }


    public function userViewPost($key,Request $request)
    {
        try {
            $userId = decrypt($key);
            $response_data["title"] = __("title.private.user_post");
            $response_data["key"] = $key;
            $user = User::find($userId);
            if($user){
                $response_data["profile"] = User::withCount(["followerList", "followedList", "posts"])->whereId($user->id)->first();
                $limit = config("site.pagination.posts");
                $response_data["posts"] = UserPost::with(["attachments:id,post_id,file,thumb_file,type","share.attachments:id,post_id,file,thumb_file,type"])->whereUserId($user->id)->orderBy("created_at", "desc")->paginate($limit);
                //dd($response_data["posts"]);
                return view("private.user.postlist")->with($response_data);
            }else{
                return redirect(route("private.users"));
            }
        } catch (DecryptException $e) {
            return redirect(route("private.users"));
        }
    }
    
    public function getCommentList(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
        [
          'post_data'       => 'required|exists:user_posts,id,active,1,deleted_at,NULL'
        ]);
        if(!$validator->fails())
        {
            $query = PostComment::with(['subComments.user:id,name,gender,profile_pic', 'user:id,name,gender,profile_pic'])->withCount('userLike')->wherePostId($request->post_data)->whereParentId(0);
            $limit = config("site.pagination.comments");
            $data = $query->paginate($limit);
            $response_data = ["success" => 1, "data" => $data];
        }
        else
        {
            $response_data = ["success" => 0,  "message" => __("validation.refresh_page")];
        }

        return response()->json($response_data);
    }

    //GETDATA FOR EDIT
    public function getPosts(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'user_data'       => 'required|exists:users,id,deleted_at,NULL'
            ]);
        if(!$validator->fails()){
            $user = auth()->user();
            $user_id = $request->user_data;

            $limit = config("site.pagination.posts");

            $posts = UserPost::with(["attachments:id,post_id,file,thumb_file,type","share.attachments:id,post_id,file,thumb_file,type", "share.user:id,name,profile_pic,gender"])->whereUserId($user_id)->orderBy("created_at", "desc")->paginate($limit);

            $response_data = ["success" => 1,  "data" => $posts];
        }else{
            $response_data = ["success" => 0,  "message" => __("validation.refresh_page")];
        }
        return response()->json($response_data);
    }

    //Post Blocked User
    public function updatePostBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "type" => "required|in:1,2",
                    "id" => "required|exists:user_posts,id",
                ]);
        //dd($request->all());
        if(!$validator->fails()){
            $userpost = UserPost::find($request->id);
            if($request->type == 2)
            {
                $userpost->blocked_at = now();
            }
            elseif($request->type==1)
            {
                $userpost->blocked_at =NULL;
            }
            $blockStatus = $request->type==1 ? "Unblock" : "Block";
            if($userpost->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "User Post $blockStatus"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }


    //Post Blocked User
    public function updateCommentBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    "type" => "required|in:1,2",
                    "id" => "required|exists:post_comments,id",
                ]);
        //dd($request->all());
        if(!$validator->fails()){
            $postcomment = PostComment::find($request->id);
            if($request->type == 2)
            {
                $postcomment->blocked_at = now();
            }
            elseif($request->type==1)
            {
                $postcomment->blocked_at =NULL;
            }
            $blockStatus = $request->type==1 ? "Unblock" : "Block";
            if($postcomment->save()){
                $response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "User Comment $blockStatus"])];
            }else{
                $response_data = ["success" => 0, "message" => __("site.server_error")];
            }
        }else{
            $response_data = ["success" =>  0, "message" => __("validation.refresh_page")];
        }
       return response()->json($response_data);
    }
}

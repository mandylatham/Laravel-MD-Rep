<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use App\UserPost;
use App\PostComment;
use App\PostLike;
use App\User;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    	$user = auth()->user();
    	$validator = Validator::make($request->all(),
        [
            'post_data'             => 'required|integer|exists:user_posts,id,active,1,deleted_at,NULL',
            'text'          	    => 'required|max:'.limit("post_comment.max"),
            'parent_comment'        => 'nullable|integer|exists:post_comments,id,deleted_at,NULL',

        ]);

		if(!$validator->fails()){
			$parent_comment =0;
			if(!empty($request->parent_comment) || $request->parent_comment!=0)
			{
				$parent_comment = $request->parent_comment;
			}
			$comment = new PostComment();
			$comment->post_id           = $request->post_data;
			$comment->text           	= $request->text;
			$comment->user_id           = $user->id;
			$comment->parent_id         = $parent_comment;
			$comment->like_count        = 0;
			$comment->created_by        = $user->id;
			$comment->updated_by        = $user->id;
			if($comment->save())
			{
                UserPost::find($request->post_data)->increment('comment_count');
				$response_data =  ['success' => 1, 'message' => __('validation.create_success',['attr'=> 'Comment'])];
			}else
			{
				$response_data = ["success" => 0, "message" => __("site.server_error")];
			}
		}
		else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        
        return response()->json($response_data);
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();
    	$validator = Validator::make($request->all(),
        [
        	'comment_data'         => 'required|integer|exists:post_comments,id,deleted_at,NULL',
            'text'          	   => 'required|max:'.limit("post_comment.max"),

        ]);
        if(!$validator->fails()){
			$comment = PostComment::whereId($request->comment_data)->first();
			$comment->text           	= $request->text;
			$comment->updated_by        = $user->id;
			if($comment->save())
			{
				$response_data =  ['success' => 1, 'message' => __('validation.update_success',['attr'=> 'Comment'])];
			}else
			{
				$response_data = ["success" => 0, "message" => __("site.server_error")];
			}
		}
		else{
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
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
          'comment_data' => 'required|exists:post_comments,id,deleted_at,NULL|checkpostuser',
        ]);
            
        if (!$validator->fails()) 
        { 
            $currentComment = PostComment::whereId($request->comment_data)->first();
            if($currentComment)
            {
              $postId = $currentComment->post_id;
              $getComment = PostComment::whereId($request->comment_data)->orwhere('parent_id',$request->comment_data)->get();
              $getCommentid = $getComment->pluck("id")->toArray();
              $like = PostLike::whereIn("likeable_id", $getCommentid)->where('likeable_type',2)->delete(); 
              $comment = PostComment::whereId($request->comment_data)->orwhere('parent_id',$request->comment_data)->delete();
              if($comment > 0){
                  PostComment::whereId($request->comment_data)->decrement('like_count',$like);
                  UserPost::find($postId)->decrement('comment_count',$comment);
                  $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'Comment'])]; 
              }else{
                  
                  $response_data =  ['success' => 0, 'message' => __('site.server_error')]; 
              }
            }
            else
            {
              $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
            }
        }else
        {
            $response_data =  ['success' => 0, 'message' => __('validation.refresh_page')];
        }

        return response()->json($response_data);
    }

    /**
     * Get Comment List specified resource from storage.
     *
     * @param  \App\PostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
        [
          'post_data'       => 'required|exists:user_posts,id,active,1,deleted_at,NULL',
          'limit'           => 'nullable|numeric',
        ]);
        if(!$validator->fails())
        {
            $query = PostComment::with(['subComments.user:id,name,gender,profile_pic', 'user:id,name,gender,profile_pic'])->withCount('userLike')->wherePostId($request->post_data)->whereParentId(0);
            
            if($request->limit > 0){
                $limit = $request->limit;
            }else{
                $limit = config("site.pagination.comments");
            }
                $data = $query->paginate($request->limit);
            $response_data = ["success" => 1, "data" => $data];
        }
        else
        {
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }

     /**
     * increment or decrement the specified resource from storage.
     *
     * @param  \App\PostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
    	$user = auth()->user();
    	$validator = Validator::make($request->all(),
            [
              'comment_data' 	      => 'required|exists:post_comments,id,deleted_at,NULL',
              'status' 		          => 'required|integer|in:0,1',
            ]);
            
        if (!$validator->fails()) 
        { 
            
            $postLike = PostLike::whereUserId($user->id)->wherelikeableId($request->comment_data)->wherelikeableType(2)->first();
            $like = "Comment liked";

            if($request->status == 1)
            {
                if(!$postLike){
                    $data = PostLike::updateOrInsert(
                        ['likeable_id' => $request->comment_data, 'likeable_type' => 2, 'user_id' => $user->id],
                        ['created_by' => $user->id, 'updated_by' => $user->id, "deleted_at" => null]
                    );
                    PostComment::find($request->comment_data)->increment('like_count');
                }
            }elseif ($request->status == 0) {
                $like = "Comment unliked";
                if($postLike){
                    $postLike->delete();
                    PostComment::find($request->comment_data)->decrement('like_count');
                }
            }
        	
    		$response_data =  ['success' => 1, 'message' => __('validation.unfollow_success',['attr'=> $like])];
        	
        }else
        {
            $response_data = ["success" => 0,  "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

        return response()->json($response_data);
    }

    public function getLikes(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
              'comment_data'       => 'required|exists:post_comments,id,deleted_at,NULL'
            ]);
        if(!$validator->fails()){
            $postlikes = PostLike::select(["user_id"])->whereLikeableId($request->comment_data)->whereLikeableType(2)->orderBy("created_at", "desc")->get();
            $users = $postlikes->pluck("user_id")->toArray();
            $likedUsers = User::select("name", "id", "gender", "profile_pic")->whereIn("id", $users)->paginate(config("site.pagination.likes"));

            $response_data = ["success" => 1,  "data" => $likedUsers];
        }else{
            $response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "Comment"])];
        }
        return response()->json($response_data);
    }


    
}

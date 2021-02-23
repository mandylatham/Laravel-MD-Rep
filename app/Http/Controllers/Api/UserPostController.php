<?php

namespace App\Http\Controllers\Api;
use Validator;
use Auth;
use App\UserPost;
use App\Follower;
use App\PostAttachment;
use App\PostComment;
use App\PostLike;
use App\PostHide;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UserPostController extends Controller
{
   
	//GETDATA FOR EDIT
	public function getPosts(Request $request)
	{
		$validator = Validator::make($request->all(),
            [
              'user_data'    	=> 'nullable|exists:users,id,active,1,deleted_at,NULL'
            ]);
		if(!$validator->fails()){
			$user = auth()->user();

			if($request->filled("user_data")){
				$user_id = $request->user_data;
			}else{
				$user_id = $user->id;
			}

			if($request->page < 2){
				$user = User::withCount(["followerList", "followedList", "posts"])->whereId($user_id)->first();
			}else{
				$user = [];
			}

			$limit = config("site.pagination.posts");

			$posts = UserPost::with(["attachments:id,post_id,file,thumb_file,type,watch_count","share.attachments:id,post_id,file,thumb_file,type,watch_count"])->whereUserId($user_id)->orderBy("created_at", "desc")->paginate($limit);

			$response_data = ["success" => 1,  "data" => $posts, "user" => $user];
		}else{
			$response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "User"])];
		}
		return response()->json($response_data);
	}


	//GETDATA FOR EDIT
	public function wall(Request $request)
	{
		$user = auth()->user();
		$record = Follower::whereFollowerId(auth()->user()->id)->select('followed_id')->get()->pluck('followed_id')->toArray();
		$record[] = $user->id;
		$limit = config("site.pagination.posts");
		$posts = UserPost::with(["user:id,name,profile_pic,gender", "attachments:id,post_id,file,thumb_file,type,watch_count","share.attachments:id,post_id,file,thumb_file,type,watch_count", "share.user:id,name,profile_pic,gender"])->whereIn("user_id", $record)
			->whereDoesntHave('postHide', function ($query) use ($user) {
				$query->where('active', '1');
				$query->where('user_id', $user->id);
			})
			->orderBy("created_at", "desc")->paginate($limit);

		$response_data = ["success" => 1,  "data" => $posts];

		return response()->json($response_data);
	}


	//INDEX
	public function getPostAttachments(Request $request)
	{
		$validator = Validator::make($request->all(),
            [
              'user_data'    	=> 'nullable|exists:users,id,active,1,deleted_at,NULL'
            ]);
		if(!$validator->fails()){
			$user = auth()->user();

			if($request->filled("user_data")){
				$user_id = $request->user_data;
			}else{
				$user_id = $user->id;
			}

			$user = User::whereId($user_id)->first();
			/*if($request->page < 2){
			}else{
				$user = [];
			}*/

			$limit = config("site.pagination.posts");

			$posts = $user->postAttachments;
			$paginate = $this->paginate($posts, $limit);
			$paginate = $paginate->toArray();
			if(isset($paginate["data"]) && count($paginate["data"]) > 0){
				$paginate["data"] = array_values($paginate["data"]);
			}
			
			$response_data = ["success" => 1,  "data" => $paginate];
		}else{
			$response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "User"])];
		}
		return response()->json($response_data);
	}

	//CREATE (POST)
	public function create(Request $request)
	{
		$post 		= new UserPost();
		$attach 	= new PostAttachment();
		$attached 	= new PostAttachment();
		
		$validator = Validator::make($request->all(),
            [
              'text'             => 'nullable|required_without_all:attachment,thumb_attach|max:'.limit("post_text.max"),
              'attachment.*'     => 'required_without_all:text,thumb_attach|mimes:'.limit("file.format").'|limit_file:attachment,'.limit("file.count"),
              'thumb_attach.*'   => 'required_without_all:text,attachment|image|mimes:'.limit("post_image.format").'|limit_file:thumb_attach,'.limit("post_image.count")
              /*'image.*'       => 'nullable|image|mimes:'.limit("post_image.format").'|limit_file:image,'.limit("post_image.count"),
              'thumb_image.*' => 'required_with:image.*||image|mimes:'.limit("post_image.format").'|limit_file:thumb_image,'.limit("post_image.count"),
              'video.*'       => 'nullable|mimes:'.limit("post_video.format").'|limit_file:video,'.limit("post_video.count"),
              'thumb_video.*' => 'required_with:video.*|image|mimes:'.limit("thumb_image.format").'|limit_file:thumb_video,'.limit("post_video.count")*/
            ]);
			
		if(!$validator->fails()){
 
			$text				= "";
			$text 				= $request->text;
			$post->text 		= $text;
			$post->type 		= 1;
			$post->user_id 		= Auth::user()->id; 
			$post->created_by 	= Auth::user()->id;
			$post->updated_by 	= Auth::user()->id;
			$created			= $post->save();
			$attachments = [];
			/*$response_data=[];*/
			$i = 0;
			if($created)
			{
				if($request->attachment > 0)
				{
					
					//SAVE POST IMAGE 
					foreach($request->attachment as $file) 
					{
						$fileType = $file->getClientMimeType();
						$image_file = ["image/jpg","image/jpeg","image/gif","image/png"];
						$video_file = ["video/mkv","video/mp4","video/avi","video/mpeg"];
						
						/*print_r($fileType);
						exit;*/
							if(in_array($fileType,$image_file))
							{

								$filePath = "post/images";
					            if($request->hasFile('attachment'))
					            { 
					            	
								   
					               		$attachment = [];
					               		$image = Storage::url($file->store($filePath));
					               		$thumb_image = Storage::url($request->file("thumb_attach.".$i++)->store($filePath));
			           		            $attachment["post_id"] 		= $post->id; 
			           		            $attachment["user_id"]		= Auth::user()->id; 
			       		            	$attachment["type"] 		= 1; 
			           					$attachment["file"] 		= $image;
			           					$attachment["thumb_file"] 	= $thumb_image;
			           					$attachment["created_by"] 	= Auth::user()->id;
			           					$attachment["updated_by"] 	= Auth::user()->id;
			           					$attachments[] = $attachment;
					           		
					            }
							}

							if(in_array($fileType,$video_file))
							{
								//SAVE POST VIDEOS
								$filePath2 = "post/videos";
				            	if($request->hasFile('attachment')){
				            		
				    		            $attachment 		= []; 
				                		$video = Storage::url($file->store($filePath2));
				                		$thumb_video = Storage::url($request->file("thumb_attach.".$i++)->store($filePath2));
				    		            $attachment["post_id"] 		= $post->id; 
				    		            $attachment["user_id"]		= Auth::user()->id; 
						            	$attachment["type"] 		= 2; 
				    					$attachment["file"] 		= $video;
				    					$attachment["thumb_file"] 	= $thumb_video;
				    					$attachment["created_by"] 	= Auth::user()->id;
				    					$attachment["updated_by"] 	= Auth::user()->id;
				    					$attachments[] = $attachment;
				            	}
							}
							
					}
						
						/*if($fileType ==  '')
						{
							//SAVE POST VIDEOS
							$filePath2 = "post/videos";
			            	if($request->hasFile('attachment')){
			            		$i = 0;
			            		
			    		            $attachment 		= []; 
			                		$video = url("storage/".$file->store($filePath2));
			                		$thumb_video = url("storage/".$request->file("thumb_attach.".$i++)->store($filePath2));
			    		            $attachment["post_id"] 		= $post->id; 
			    		            $attachment["user_id"]		= Auth::user()->id; 
					            	$attachment["type"] 		= 1; 
			    					$attachment["file"] 		= $video;
			    					$attachment["thumb_file"] 	= $thumb_video;
			    					$attachment["created_by"] 	= Auth::user()->id;
			    					$attachment["updated_by"] 	= Auth::user()->id;
			    					$attachments[] = $attachment;
			            	}
						}*/    
			    }/*else
			    {
			    	$response_data = ["success" => 1, "message" => "no attachement"];
			    }*/
			}//CREATED END

			if(count($attachments) > 0){
				$attchementResult = PostAttachment::insert($attachments);
			}
			
			$response_data = ["success" => 1, "message" => __("validation.create_success", ["attr" => "Post"])];
                        
        }//VALIDATOR END
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }

       	return response()->json($response_data);
	}

	//UPDATE POST
	public function update(Request $request)
	{
		Log::debug($request->all());
		$post 		    = new UserPost();
		$attached 	    = new PostAttachment();
		$validator = Validator::make($request->all(),
            [
				'post_id'	    => 'required|numeric|exists:user_posts,id,active,1,deleted_at,NULL',
				'text'          => 'nullable|required_without_all:attachment,thumb_attach|max:'.limit("post_text.max"),
             	'attachment.*'   => 'required_without_all:text,thumb_attach|mimes:'.limit("file.format").'|limit_file:attachment,'.limit("file.count"),
                'thumb_attach.*'   => 'required_without_all:text,attachment|image|mimes:'.limit("post_image.format").'|limit_file:thumb_attach,'.limit("post_image.count"),
                'delete_attachment.*'   => 'nullable|integer'
            ]);

		if(!$validator->fails()){
			$text       = "";
			$post 		= UserPost::whereId($request->post_id);
			$text 		= $request->text;
			$data 		= ['text' => $text ];
			$updated 	= $post->update($data);
			$attachments = [];
			$i = 0;
			
			if($updated)
			{
				if($request->has("delete_attachment") && count($request->delete_attachment)> 0){
					$delete 	= PostAttachment::wherePostId($request->post_id)->whereIn("id", $request->delete_attachment)->delete();
				}
				if($request->has("attachment") && count($request->attachment)> 0){
					foreach($request->attachment as $file) 
					{
						$fileType = $file->getClientMimeType();
						$image_file = ["image/jpg","image/jpeg","image/gif","image/png"];
						$video_file = ["video/mkv","video/mp4","video/avi","video/mpeg"];
						
						/*print_r($fileType);
						exit;*/
							if(in_array($fileType,$image_file))
							{
								$filePath = "post/images";
					            if($request->hasFile('attachment'))
					            { 
					            	$attachment = [];
					               		$image = Storage::url($file->store($filePath));
					               		$thumb_image = Storage::url($request->file("thumb_attach.".$i++)->store($filePath));
			           		            $attachment["post_id"] 		= $request->post_id; 
			           		            $attachment["user_id"]		= Auth::user()->id; 
			       		            	$attachment["type"] 		= 1; 
			           					$attachment["file"] 		= $image;
			           					$attachment["thumb_file"] 	= $thumb_image;
			           					$attachment["created_by"] 	= Auth::user()->id;
			           					$attachment["updated_by"] 	= Auth::user()->id;
			           					$attachments[] = $attachment;
					            }
							}

							if(in_array($fileType,$video_file))
							{
								//SAVE POST VIDEOS
								$filePath2 = "post/videos";
				            	if($request->hasFile('attachment')){
				            		
				    		            $attachment 		= []; 
				                		$video = Storage::url($file->store($filePath2));
				                		$thumb_video = Storage::url($request->file("thumb_attach.".$i++)->store($filePath2));
				    		            $attachment["post_id"] 		= $request->post_id; 
				    		            $attachment["user_id"]		= Auth::user()->id; 
						            	$attachment["type"] 		= 2; 
				    					$attachment["file"] 		= $video;
				    					$attachment["thumb_file"] 	= $thumb_video;
				    					$attachment["created_by"] 	= Auth::user()->id;
				    					$attachment["updated_by"] 	= Auth::user()->id;
				    					$attachments[] = $attachment;
				            	}
							}
					}
				}
						    	
			}//UPDATED END

			if($request->has("attachment") && count($attachments) > 0){
				$attchementResult = PostAttachment::insert($attachments);
			}

			$response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Post"])];
                        
        }//VALIDATOR END
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
        Log::debug($response_data);

       	return response()->json($response_data);
	}


	//POST DESTROY
    public function destroy(Request $request)
    {
        $validator  = Validator::make($request->all(),
            [
              'post_id' => 'required|integer|exists:user_posts,id,active,1,deleted_at,NULL',
            ]);

        if (!$validator->fails()) 
            { 
                $post 	 		= UserPost::whereId($request->post_id);
                $parent 		= UserPost::whereId($request->post_id)->first();
                $check_parent 	= UserPost::whereId($parent->parent_id)->first();
                if($check_parent)
                {
					if($parent->type !=1)
					{
						UserPost::find($parent->parent_id)->decrement('share_count');
					}
				}   
                $deleted = $post->delete();
                if($deleted)
                {
					$attached 	= PostAttachment::wherePostId($request->post_id);    
                	$attached->delete();

                	$like 	= PostLike::wherelikeableId($request->post_id);    
                	$like->delete();
                }
                $response_data =  ['success' => 1, 'message' => __('validation.delete_success',['attr'=>'post'])]; 
            }else
            {
                $response_data =  ['success' => 0, 'message' => __('validation.check_fields') , 'errors' => $validator->errors()];
            }

        return response()->json($response_data);
    }
	
    //CREATE LIKE
    public function like(Request $request)
    {
		$validator = Validator::make($request->all(),
            [
              'post_data'    => 'required|numeric',
              'status'	   =>' required|numeric|in:0,1',
            ]);

		if(!$validator->fails()){
			$user  = Auth::user();
			
			$postLike = PostLike::whereUserId($user->id)->wherelikeableId($request->post_data)->wherelikeableType(1)->first();
			$like = "Post liked";

			if($request->status == 1)
			{
				if(!$postLike){
					$data = PostLike::updateOrInsert(
                        ['likeable_id' => $request->post_data, 'likeable_type' => 1, 'user_id' => $user->id],
                        ['created_by' => $user->id, 'updated_by' => $user->id, "deleted_at" => null]
                    );
                    UserPost::find($request->post_data)->increment('like_count');
				}
			}elseif ($request->status == 0) {
				$like = "Post unliked";
				if($postLike){
					$postLike->delete();
					UserPost::find($request->post_data)->decrement('like_count');
				}
			}

			$response_data =  ['success' => 1, 'message' => __('validation.unfollow_success',['attr'=> $like])];

			
        }else
            {
                $response_data =  ['success' => 0, 'message' => __('validation.check_fields') , 'errors' => $validator->errors()];
            }
        return response()->json($response_data);
    }



	public function getLike(Request $request)
	{
		$validator = Validator::make($request->all(),
            [
              'post_data'    	=> 'required|exists:user_posts,id,active,1,deleted_at,NULL'
            ]);
		if(!$validator->fails()){
			$postlikes = PostLike::select(["user_id"])->whereLikeableId($request->post_data)->whereLikeableType(1)->orderBy("created_at", "desc")->get();
			$users = $postlikes->pluck("user_id")->toArray();
			$likedUsers = User::select("name", "id", "gender", "profile_pic")->whereIn("id", $users)->paginate(config("site.pagination.likes"));

			$response_data = ["success" => 1,  "data" => $likedUsers];
		}else{
			$response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "User"])];
		}
		return response()->json($response_data);
	}


	//UPDATE POST
	public function share(Request $request)
	{
		$post 		= new UserPost();
		$attach 	= new PostAttachment();
		$attached 	= new PostAttachment();
		
		$validator = Validator::make($request->all(),
            [
              'post_data'	    => 'required|numeric',
			  'text'          => 'nullable|required_without_all:attachment,thumb_attach|max:'.limit("post_text.max"),
              'attachment.*'   => 'required_without_all:text,thumb_attach|mimes:'.limit("file.format").'|limit_file:attachment,'.limit("file.count"),
              'thumb_attach.*'   => 'required_without_all:text,attachment|image|mimes:'.limit("post_image.format").'|limit_file:thumb_attach,'.limit("post_image.count")
            ]);

		if(!$validator->fails()){
 
			$text				= "";
			$text 				= $request->text;
			$post->text 		= $text;
			$post->parent_id 	= $request->post_data;
			$post->type 		= 2;
			$post->user_id 		= Auth::user()->id; 
			$post->created_by 	= Auth::user()->id;
			$post->updated_by 	= Auth::user()->id;
			$created			= $post->save();
			UserPost::find($request->post_data)->increment('share_count');
			$attachments = [];
			$i=0;
			/*if($created)
			{
				if($request->attachment > 0)
					//SAVE POST IMAGE 
					foreach($request->attachment as $file) 
					{
						$fileType = $file->getClientMimeType();
						$image_file = ["image/jpg","image/jpeg","image/gif","image/png"];
						$video_file = ["video/mkv","video/mp4","video/avi","video/mpeg"];
						if(in_array($fileType,$image_file))
						{
							$filePath = "post/images";
				            if($request->hasFile('attachment'))
				            { 
				               		$attachment = [];
				               		$image = url("storage/".$file->store($filePath));
				               		$thumb_image = url("storage/".$request->file("thumb_attach.".$i++)->store($filePath));
		           		            $attachment["post_id"] 		= $post->id; 
		           		            $attachment["user_id"]		= Auth::user()->id; 
		       		            	$attachment["type"] 		= 1; 
		           					$attachment["file"] 		= $image;
		           					$attachment["thumb_file"] 	= $thumb_image;
		           					$attachment["created_by"] 	= Auth::user()->id;
		           					$attachment["updated_by"] 	= Auth::user()->id;
		           					$attachments[] = $attachment;
				            }
						}
						if(in_array($fileType,$video_file))
						{
							//SAVE POST VIDEOS
							$filePath2 = "post/videos";
			            	if($request->hasFile('attachment')){
			            		
			    		            $attachment 		= []; 
			                		$video = url("storage/".$file->store($filePath2));
			                		$thumb_video = url("storage/".$request->file("thumb_attach.".$i++)->store($filePath2));
			    		            $attachment["post_id"] 		= $post->id; 
			    		            $attachment["user_id"]		= Auth::user()->id; 
					            	$attachment["type"] 		= 2; 
			    					$attachment["file"] 		= $video;
			    					$attachment["thumb_file"] 	= $thumb_video;
			    					$attachment["created_by"] 	= Auth::user()->id;
			    					$attachment["updated_by"] 	= Auth::user()->id;
			    					$attachments[] = $attachment;
			            	}
						}	
					}
				}
			}//CREATED END
			if(count($attachments) > 0){
				$attchementResult = PostAttachment::insert($attachments);
			}*/
			if($created)
			{
				$response_data = ["success" => 1, "message" => __("validation.share_success", ["attr" => "Post"])];
			}else{
				$response_data = ["success" => 0, "message" => __("site.server_error")];
			}
                        
        }//VALIDATOR END
        else
        {
            $response_data = ["success" => 0, "message" => __("validation.check_fields"), "errors" => $validator->errors()->toArray()];
        }
       	return response()->json($response_data);
	}
	//Hide post
	public function hidePost(Request $request)
	{
		$validator = Validator::make($request->all(),
            [
              'post_data'    	=> 'required|exists:user_posts,id,active,1,deleted_at,NULL',
              'status'	   		=> ' required|numeric|in:0,1'
            ]);
		if(!$validator->fails()){
			$hidepost = new PostHide();
			$hidepost->post_id 	= $request->post_data;
			$hidepost->active 	= $request->status;
			$hidepost->user_id 		= Auth::user()->id; 
			$hidepost->created_by 	= Auth::user()->id;
			$hidepost->updated_by 	= Auth::user()->id;
			if($hidepost->save())
			{
				$response_data = ["success" => 1, "message" => __("validation.post_hide_success", ["attr" => "Post"])];
			}
			else
			{
				$response_data = ["success" => 0,  "message" =>__("site.server_error")];
			}
		}else{
			$response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "Post Hide"])];
		}
		return response()->json($response_data);
	}
	

	//Hide post
	public function videoCount(Request $request)
	{
		$validator = Validator::make($request->all(),
            [
              'data'    	=> 'required|exists:post_attachments,id,type,2,active,1,deleted_at,NULL',
            ]);
		if(!$validator->fails()){
			$attachment = PostAttachment::find($request->data);
			$post = $attachment->post;
			if(empty($post->blocked_at)){
				$attachment->increment("watch_count");
				$response_data = ["success" => 1, "message" => __("validation.update_success", ["attr" => "Count"])];
			}else{
				$response_data = ["success" => 0,  "message" =>__("validation.post_blocked")];
			}
			
		}else{
			$response_data = ["success" => 0,  "message" => __("validation.not_found", ["attr" => "Post Hide"])];
		}
		return response()->json($response_data);
	}
	
}

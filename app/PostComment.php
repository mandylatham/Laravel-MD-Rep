<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PostLike;

class PostComment extends Model
{
	use SoftDeletes;
   protected $appends  = ['liked_by_user'];
   protected $fillable  = ['post_id','user_id','parent_id','like_count','text','created_by','updated_by','blocked_at'];
   protected $visible   = ['id', 'user_id','parent_id','like_count','text', 'subComments', 'user', 'liked_by_user', 'created_at','blocked_at'];

   /**
     * Get the post that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo('App\User',"user_id", "id");
    }

    /**
     * Get the post that owns the comment.
     */
    public function like()
    {
        return $this->belongsTo('App\PostLike',"likeable_id", "id")->where('type',2);
    }

    /**
     * Get the post that owns the comment.
     */
    public function subComments()
    {
        return $this->hasMany('App\PostComment',"parent_id", "id")->where('parent_id', "!=", 0);
    }

    /**
     * Get the post that owns the comment.
     */
    public function getLikedByUserAttribute()
    {
        $likeCount = 0;
        if(auth()->user()){
            $like = PostLike::whereUserId(auth()->user()->id)->whereLikeableId($this->id)->whereLikeableType(2)->first();
            if($like){
                $likeCount = 1;
            }
        }
        return $this->attributes["liked_by"] = $likeCount;
    }


    /**
     * Get the post that owns the comment.
     */
    public function userLike()
    {
        return $this->hasMany("App\PostLike", "likeable_id", "id")->whereLikeableType(2)->whereUserId(auth()->user()->id);
    }

}

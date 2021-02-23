<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\PostLike;

class UserPost extends Model
{
     use SoftDeletes;

     protected $appends = ['liked_by_user'];

     /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function attachments()
    {
        return $this->hasMany("App\PostAttachment", "post_id", "id");
    }

    public function likes()
    {
        return $this->hasMany("App\PostLike", "likeable_id", "id");
    }

    public function likedUsers()
    {
         return $this->morphToMany('App\User', 'likeable');
    }

    public function user()
    {
        return $this->hasMany("App\User", "id", "user_id");
    }

    public function share()
    {
        return $this->hasOne("App\UserPost", "id", "parent_id");
    }

    public function followed()
    {
        return $this->hasMany("App\PostAttachment", "post_id", "id");
    }

    public function comments()
    {
        return $this->hasMany("App\PostAttachment", "post_id", "id")->where("parent_id", 0);
    }
    public function postHide()
    {
        return $this->hasMany("App\PostHide", "post_id", "id");
    }
    /**
     * Get the post is liked by user
     */
    public function getLikedByUserAttribute()
    {
        $likeCount = 0;
        if(auth()->user()){
            $like = PostLike::whereUserId(auth()->user()->id)->whereLikeableId($this->id)->whereLikeableType(1)->first();
            if($like){
                $likeCount = 1;
            }
        }
        return $this->attributes["liked_by"] = $likeCount;
    }
}

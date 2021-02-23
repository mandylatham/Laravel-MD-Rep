<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatGroup extends Model
{
    use SoftDeletes;

    /**
     * Get the post that owns the comment.
     */
    public function chats()
    {
        return $this->hasMany('App\ChatMessage',"chat_id", "id");
    }

    /**
     * Get the post that owns the comment.
     */
    public function toUser()
    {
        return $this->belongsTo('App\User',"to_user", "id");
    }

    /**
     * Get the post that owns the comment.
     */
    public function fromUser()
    {
        return $this->belongsTo('App\User',"from_user", "id");
    }

        /**
     * Get the post that owns the comment.
     */
    public function unread()
    {
        return $this->hasMany('App\ChatMessage',"chat_id", "id")->where("is_read", 0);
    }

    /**
     * Get the post that owns the comment.
     */
    public function lastChat()
    {
        return $this->hasOne('App\ChatMessage',"chat_id", "id")->orderBy("id","desc");
    }
}

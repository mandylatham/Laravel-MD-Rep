<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;


    /**
     * Get the post that owns the comment.
     */
    public function toUser()
    {
        return $this->belongsTo('App\User',"to_user", "id");
    }
}

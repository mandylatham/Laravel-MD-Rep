<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostAttachment extends Model
{
    use SoftDeletes;

    public function post()
    {
    	return $this->belongsTo("App\UserPost", "post_id", "id");
    }
}

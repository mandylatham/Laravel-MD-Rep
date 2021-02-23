<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PostLike extends Model
{
	use SoftDeletes;
    protected $fillable = ['likeable_id','user_id','type','created_by','updated_by'];
}

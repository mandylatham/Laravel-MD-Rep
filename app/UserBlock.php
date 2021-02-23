<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlock extends Model
{
	use SoftDeletes;
    protected $fillable  = ['user_id','block_user','status','created_by','updated_by'];


    public function blockUser()
    {
        return $this->hasMany("App\User", "id", "block_user");
    }
}

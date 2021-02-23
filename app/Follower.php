<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follower extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'follower_id','followed_id', 'created_by','updated_by',
    ];

    /**
     * Get the follwer of project
     */
    public function user()
    {
        return $this->belongsTo('App\User', "follower_id", "id");
    }

    /**
     * Get the follwed of project
     */
    public function follwed()
    {
        return $this->belongsToMany('App\User', "followed_id", "id");
    }


}

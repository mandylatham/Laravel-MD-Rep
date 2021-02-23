<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FullTextSearch;
use App\Gender;
use App\Follower;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes,FullTextSearch;

    protected $appends = ['change_password', 'key', "gender_name", "follow"];

    protected $searchable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'tel_code', 'bio', 'phone', 'gender', 'active', 'profile_pic', 'created_by', 'updated_by', 'blocked_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * Set the user's email.
     *
     * @param  string  $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Set the user's  name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }

    /**
     * Set the user's  name.
     *
     * @param  string  $value
     * @return void
     */
    public function getChangePasswordAttribute()
    {
        return $this->attributes['change_password'] = empty($this->password) ? 0 : 1;
    }

    /**
     * Set the user's  secxret.
     *
     * @param  string  $value
     * @return void
     */
    public function getKeyAttribute()
    {
        return encrypt($this->id);
    }


    /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function genderType()
    {
        return $this->belongsTo("App\Gender", "gender", "id");
    }

    /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function posts()
    {
        return $this->hasMany("App\UserPost", "user_id", "id");
    }

    /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function follower()
    {
        return $this->hasMany("App\Follower", "follower_id", "id");
    }

    /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function bookedEvents()
    {
        return $this->belongsToMany("App\Event", "event_books", "user_id", "event_id")->wherePivot('status', 1)->wherePivot('deleted_at', NULL)->withTimestamps()->withPivot('id');
    }

    /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function postAttachments()
    {
        return $this->hasManyThrough("App\PostAttachment", "App\UserPost", "user_id", "post_id", "id", "id")->orderBy("id", "desc");
    }

    /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function followerList()
    {
        return $this->belongsToMany("App\User", "followers", "follower_id", "followed_id")->wherePivot('deleted_at', NULL)->orderBy('name', 'asc');
    }

        /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function followedList()
    {
        return $this->belongsToMany("App\User", "followers", "followed_id", "follower_id")->wherePivot("deleted_at", NULL)->orderBy('name', 'asc');
    }

    /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function follwed()
    {
        return $this->hasMany("App\Follower", "followed_id", "id");
    }


    /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function getGenderNameAttribute()
    {
        $gender = Gender::find($this->gender);
        $genderName = "";
        if($gender){
            $genderName = $gender->name;
        }
        return $genderName;
    }

        /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function getFollowAttribute()
    {
    	$follwed = 0;

    	if(auth()->user() && auth()->user()->id != $this->id){
	    	$user = Follower::where("followed_id", $this->id)->whereFollowerId(auth()->user()->id)->first();
	    	if($user){
		    	$follwed = 1;
	    	}
    	}
        return $this->attributes['follow'] = $follwed;
    }

    /**
     * Set Relationship to follower table
     *
     * @param  string  $value
     * @return void
     */
    public function wallPosts()
    {
        return $this->hasManyThrough("App\UserPost", "App\Follower", "user_id", "user_id", "id", "user_id");
    }


    /**
     * List of blocked users
     *
     * @param  string  $value
     * @return void
     */
    public function blockedUsers()
    {
        return $this->hasMany("App\UserBlock", "block_user", "id")->where("status", 1);
    }



}

<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdminPermission;

class AdminUser extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $appends = ["type_name"];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone' , 'user_type', 'code', 'created_by', 'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function userType()
    {
        return $this->belongsTo("App\AdminUserType", "user_type", "id");
    }

    /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function getTypeNameAttribute()
    {
        return $this->userType->name;
    }

    /**
     * Set Relationship to gender table
     *
     * @param  string  $value
     * @return void
     */
    public function getisAdminAttribute()
    {
        return $this->userType->name;
    }

    /**
     * Check  admin user type
     *
     * @param  string  $value
     * @return void
     */
    public function isAdmin()
    {
        return $this->userType->admin_type == 1;
    }

    /**
     * Check club user type
     *
     * @param  string  $value
     * @return void
     */
    public function isClub()
    {
        return $this->userType->admin_type == 0;
    }

    /**
     * Check admin user type
     *
     * @param  string  $value
     * @return void
     */
    public function isSubAdmin()
    {
        return $this->userType->admin_type == 2;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }

    //Check user is other user
    public function ican($permission)
    {
        if($this->isAdmin()){
            return true;
        }
        $role = $this->user_type;
        $permissions = Permission::where("code", $permission)->whereActive(1)->first();
        if($permissions){
            $userPermission = AdminPermission::where("user_type", $role)->where("permission", $permissions->id)->whereActive(1)->first();
            if($userPermission){
                return true;
            }    
        }
        
        return false;
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


}

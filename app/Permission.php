<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    public function assignedPermission()
    {
    	return $this->hasOne("App\AdminPermission", "permission", "id")->whereActive(1);
    }
}

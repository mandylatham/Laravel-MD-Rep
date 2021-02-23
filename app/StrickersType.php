<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StrickersType extends Model
{
    use SoftDeletes;

    public function stickers()
    {
    	return $this->hasMany("App\Sticker", "type", "id")->where("active", 1);
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
}

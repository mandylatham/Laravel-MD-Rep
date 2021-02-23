<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventType extends Model
{
    use SoftDeletes;

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

    public function eventType()
    {
        return $this->hasMany('App\EventType', "parent_id", "id");
    }

    public function parentType()
    {
        return $this->belongsTo('App\EventType', "parent_id", "id");
    }

}

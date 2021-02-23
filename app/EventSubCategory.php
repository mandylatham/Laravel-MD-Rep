<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubCategory extends Model
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

    /**
     * Get the follwer of project
     */
    public function eventType()
    {
        return $this->belongsTo('App\EventType', "event_type", "id");
    }
}

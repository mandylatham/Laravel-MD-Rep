<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBook extends Model
{
    use SoftDeletes;

    /**
     * Set Relationship to Event Type table
     *
     * @param  string  $value
     * @return void
     */
    public function event()
    {
        return $this->belongsTo("App\Event", "event_id", "id");
    }
}

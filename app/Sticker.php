<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sticker extends Model
{
    use SoftDeletes;
    /**
     * Set Relationship to Event Type table
     *
     * @param  string  $value
     * @return void
     */
    public function stickerType()
    {
        return $this->belongsTo("App\StrickersType", "type", "id");
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

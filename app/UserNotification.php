<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Event;

class UserNotification extends Model
{
    use SoftDeletes;

    protected $appends = ["data"];

    public function event(){
        return $this->belongsTo("App\Event", "action", "id");
    }
    

    public function getDataAttribute(){
    	if($this->type == "EVENT"){
    		return Event::whereId($this->action)->first();
    	}else{
    		return [];
    	}
    }
}

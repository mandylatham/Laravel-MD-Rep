<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminActivityHistory extends Model
{
    use SoftDeletes;
}

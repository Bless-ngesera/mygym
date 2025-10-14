<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleClass extends Model
{
    //
    use HasFactpory;

    public function instructor(){
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function classType(){
        return $this->belongsTo(Classtype::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    //
    use HasFactpory;

    public function scheduledClasses(){
        return $this->hasMany(ScheduledClass::class);
    }
}

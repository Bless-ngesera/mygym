<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = ['name','email','phone','specialty','experience','photo'];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'instructor_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['member_id','instructor_id','amount','paid_at'];

    protected $dates = ['paid_at'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}

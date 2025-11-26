<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutSchedule extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'start_date',
        'end_date',
        'expected_amount',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'order_position',
        'can_pay',
        'can_pay_until',
    ];

    protected $casts = [
        'can_pay' => 'boolean',
        'can_pay_until' => 'date',
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




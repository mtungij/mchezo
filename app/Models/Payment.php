<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'group_id',
        'member_id',
        'payer_id',
        'amount',
    ];
}
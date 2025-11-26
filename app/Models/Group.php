<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'owner_id',
    'name',
    'description',
    'invite_code',
    'max_members',
    'contribution_amount',
    'start_date',
    'frequency_type',
    'interval',
    ];

    // RELATION: Owner of the group
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // RELATION: Members of the group
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('order_position')
                    ->withTimestamps();
    }

    // RELATION: GroupMember entries
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    // RELATION: Payout schedules
    public function payouts()
    {
        return $this->hasMany(PayoutSchedule::class);
    }


}
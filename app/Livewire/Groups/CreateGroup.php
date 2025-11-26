<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Group;
use App\Models\GroupMember;





class CreateGroup extends Component
{

      public $name;
    public $description;
    public $max_members;
    public $group;

    public $contribution_amount;
public $start_date;
public $frequency_type = 'day';
public $interval = 1;

    protected $rules = [
    'name' => 'required|string|min:3',
    'max_members' => 'nullable|integer|min:2',
    'contribution_amount' => 'required|numeric|min:100',
    'start_date' => 'required|date',
    'frequency_type' => 'required|in:day,week,month',
    'interval' => 'required|integer|min:1',
];

    public function createGroup()
{
    $this->validate();

      
    $this->group = Group::create([
        'owner_id' => auth()->id(),
        'name' => $this->name,
        'description' => $this->description,
        'invite_code' => strtoupper(Str::random(8)),
        'max_members' => $this->max_members,
        'contribution_amount' => $this->contribution_amount,
        'start_date' => $this->start_date,
        'frequency_type' => $this->frequency_type,
        'interval' => $this->interval,
    ]);


    // dd($this->group->toArray());


    // Add owner as first member
    GroupMember::create([
        'group_id' => $this->group->id,
        'user_id' => auth()->id(),
        'order_position' => 1,
    ]);

    session()->flash('success', 'Group created successfully! Invite link is ready.');
}

    public function render()
    {
        return view('livewire.groups.create-group');
    }
}



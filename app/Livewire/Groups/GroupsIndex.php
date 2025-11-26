<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;

class GroupsIndex extends Component
{
    public $groups;

    public function mount()
    {
        // Get all groups owned by admin
        $this->groups = Group::where('owner_id', auth()->id())->get();
    }

    public function render()
    {
        return view('livewire.groups.groups-index');
    }
}

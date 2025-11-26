<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use App\Models\GroupMember;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class RegisterThroughInvite extends Component
{
    public $group;
    public $name;
    public $phone;

    public function mount($code)
    {
        // Find group by invite code
        $this->group = Group::where('invite_code', $code)->firstOrFail();
    }

    protected $rules = [
        'name' => 'required|string|min:3',
        'phone' => 'required|string|min:9',
    ];

    public function register()
    {
        $this->validate();

        // Create user with unique login code
          $user = User::create([
        'name' => $this->name,
        'phone' => $this->phone,
        'email' => $this->phone.'@example.com',
        'password' => Hash::make(Str::random(8)),
        'login_code' => strtoupper(Str::random(4)),
    ]);

        // Add member to group with order_position
        $order = $this->group->members()->count() + 1;

        GroupMember::create([
            'group_id' => $this->group->id,
            'user_id' => $user->id,
            'order_position' => $order,
        ]);

        // Optional: Send login code via SMS
        // SmsService::send($this->phone, "Your login code: ".$user->login_code);

        session()->flash('success', "Registration successful! Your login code: ".$user->login_code);

        // Clear input fields
        $this->name = '';
        $this->phone = '';
    }

    public function render()
    {
        return view('livewire.groups.register-through-invite');
    }
}
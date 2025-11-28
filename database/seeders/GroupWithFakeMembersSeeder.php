<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;

class GroupWithFakeMembersSeeder extends Seeder
{
     public function run()
    {
        // Unda fake owner au tumia user halisi
        $owner = User::first() ?? User::factory()->create();

        // Unda group
        $group = Group::create([
            'owner_id' => $owner->id,
            'name' => 'mavuno Group',
            'description' => 'This is a test group with fake members.',
            'invite_code' => strtoupper(Str::random(8)),
            'max_members' => 25,
            'contribution_amount' => 10000,
            'start_date' => Carbon::create(2025, 10, 25), // fixed date
            'frequency_type' => 'day',
            'interval' => 5,
        ]);

        // Add owner as first member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $owner->id,
            'order_position' => 1,
        ]);

        // Unda 22 fake users na uwaingize group
        for ($i = 1; $i <= 21; $i++) {
            $user = User::create([
                'name' => 'Fake User '.$i,
                'phone' => '25570000'.str_pad($i, 2, '0', STR_PAD_LEFT),
                'email' => 'fakeuser'.$i.'@exple.com',
                'passport' => 'fake_passport_'.$i.'.jpg', // placeholder
                'password' => Hash::make(Str::random(8)),
                'login_code' => strtoupper(Str::random(4)),
            ]);

            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'order_position' => $i + 1, // owner ni 1
            ]);
        }

        $this->command->info('Group with 22 fake members created successfully!');
    }
}

<?php

use App\Livewire\Dashboard\Overview;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

test('dashboard overview computes group counts and chart data for owned groups', function () {
    $user = User::factory()->create();

    $g1 = Group::create([
        'owner_id' => $user->id,
        'name' => 'Alpha Group',
        'description' => 'Test',
        'invite_code' => 'A1',
        'max_members' => 10,
        'contribution_amount' => 1000,
        'start_date' => Carbon::today()->subDays(10)->toDateString(),
        'frequency_type' => 'day',
        'interval' => 1,
    ]);

    $g2 = Group::create([
        'owner_id' => $user->id,
        'name' => 'Beta Group',
        'description' => 'Test',
        'invite_code' => 'B1',
        'max_members' => 10,
        'contribution_amount' => 1000,
        'start_date' => Carbon::today()->subDays(5)->toDateString(),
        'frequency_type' => 'day',
        'interval' => 1,
    ]);

    $u1 = User::factory()->create();
    $u2 = User::factory()->create();
    $u3 = User::factory()->create();

    GroupMember::create(['group_id' => $g1->id, 'user_id' => $u1->id, 'order_position' => 1]);
    GroupMember::create(['group_id' => $g1->id, 'user_id' => $u2->id, 'order_position' => 2]);

    GroupMember::create(['group_id' => $g2->id, 'user_id' => $u3->id, 'order_position' => 1]);

    Livewire::actingAs($user)
        ->test(Overview::class)
        ->assertSet('createdGroupsCount', 2)
        ->assertSet('groupsChartLabels', ['Alpha Group', 'Beta Group'])
        ->assertSet('groupsChartData', [2, 1])
        ->assertSet('paidCount', 0)
        ->assertSet('unpaidCount', 3)
        ->assertSet('joinedGroupsCount', 0);
});


test('dashboard overview builds upcoming payouts list for owned groups (date vs names and group)', function () {
    $user = User::factory()->create();

    $group = Group::create([
        'owner_id' => $user->id,
        'name' => 'Gamma Group',
        'description' => 'Test',
        'invite_code' => 'G1',
        'max_members' => 10,
        'contribution_amount' => 1000,
        'start_date' => Carbon::today()->subDays(1)->toDateString(),
        'frequency_type' => 'day',
        'interval' => 1,
    ]);

    $memberA = User::factory()->create(['name' => 'Alice']);
    $memberB = User::factory()->create(['name' => 'Bob']);

    GroupMember::create(['group_id' => $group->id, 'user_id' => $memberA->id, 'order_position' => 1]);
    GroupMember::create(['group_id' => $group->id, 'user_id' => $memberB->id, 'order_position' => 2]);

    $component = Livewire::actingAs($user)->test(Overview::class);

    $upcoming = $component->get('upcomingList');

    $this->assertNotEmpty($upcoming);

    $first = $upcoming[0];
    $this->assertArrayHasKey('items', $first);
    $this->assertTrue(collect($first['items'])->contains(fn($it) => $it['member_name'] === 'Alice' && $it['group_name'] === 'Gamma Group'));

    $tooltip = $component->get('upcomingTooltipMap');
    $contains = false;
    foreach ($tooltip as $date => $lines) {
        if (in_array('Alice — Gamma Group', $lines)) {
            $contains = true;
            break;
        }
    }

    $this->assertTrue($contains, 'Tooltip map should contain "Alice — Gamma Group"');
});
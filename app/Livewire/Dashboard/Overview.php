<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Group;
use App\Models\Payment;
use Carbon\Carbon;

class Overview extends Component
{
    public $createdGroupsCount = 0;
    public $ownedGroups = [];
    public $joinedGroups = [];

    public $groupsChartLabels = [];
    public $groupsChartData = [];

    public $upcomingChartLabels = [];
    public $upcomingChartData = [];
    public $upcomingList = [];

    public $paidCount = 0;
    public $unpaidCount = 0;
    public $joinedGroupsCount = 0;

    public $upcomingTooltipMap = [];

    public function mount()
    {
        $userId = auth()->id();

        $owned = Group::withCount('groupMembers')->where('owner_id', $userId)->get();

        $this->createdGroupsCount = $owned->count();

        $this->ownedGroups = $owned->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'members_count' => $g->group_members_count ?? 0,
            ];
        })->toArray();

        $this->groupsChartLabels = $this->ownedGroups ? array_column($this->ownedGroups, 'name') : [];
        $this->groupsChartData = $this->ownedGroups ? array_column($this->ownedGroups, 'members_count') : [];

        $totalMembersAll = 0;
        $paid = 0;

        foreach ($owned as $g) {
            $members = $g->groupMembers()->get();
            $totalMembersAll += $members->count();

            foreach ($members as $m) {
                $amountDue = $g->contribution_amount * $g->interval * $g->groupMembers()->count();
                $amountPaid = Payment::where('group_id', $g->id)
                    ->where('member_id', $m->id)
                    ->sum('amount');

                if ($amountPaid >= $amountDue) {
                    $paid++;
                }
            }
        }

        $this->paidCount = $paid;
        $this->unpaidCount = max(0, $totalMembersAll - $paid);

        $joined = Group::whereHas('groupMembers', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('owner_id', '!=', $userId)->get();

        $this->joinedGroups = $joined->map(function ($g) {
            return ['id' => $g->id, 'name' => $g->name];
        })->toArray();

        $this->joinedGroupsCount = $joined->count();

        // upcoming payouts window
        $from = Carbon::today();
        $to = Carbon::today()->copy()->addDays(30);

        $items = [];

        foreach ($owned as $group) {
            $members = $group->groupMembers()->with('user')->get();
            foreach ($members as $member) {
                $payDate = $this->computePayDateForMember($group, $member);
                if ($payDate->between($from, $to)) {
                    $items[] = [
                        'date' => $payDate->format('Y-m-d'),
                        'label' => $payDate->format('d M, Y'),
                        'member_name' => $member->user->name ?? '-',
                        'group_name' => $group->name,
                    ];
                }
            }
        }

        $grouped = collect($items)->groupBy('date')->map(function ($groupItems, $date) {
            return [
                'date' => $date,
                'label' => $groupItems->first()['label'],
                'count' => $groupItems->count(),
                'items' => $groupItems->values()->all(),
            ];
        })->sortKeys()->values();

        $this->upcomingList = $grouped->toArray();
        $this->upcomingChartLabels = $grouped->pluck('label')->toArray();
        $this->upcomingChartData = $grouped->pluck('count')->toArray();

        $tooltipMap = [];
        foreach ($this->upcomingList as $row) {
            $lines = [];
            foreach ($row['items'] as $item) {
                $lines[] = ($item['member_name'] ?? '-') . ' — ' . ($item['group_name'] ?? '-');
            }
            $tooltipMap[$row['label']] = $lines;
        }

        $this->upcomingTooltipMap = $tooltipMap;
    }

    private function computePayDateForMember($group, $member)
    {
        $startDate = Carbon::parse($group->start_date);
        $interval = $group->interval;
        $position = $member->order_position;

        switch ($group->frequency_type) {
            case 'day':
                $payDate = $startDate->copy()->addDays($interval * $position);
                break;
            case 'week':
                $payDate = $startDate->copy()->addWeeks($interval * $position);
                break;
            case 'month':
            default:
                $payDate = $startDate->copy()->addMonths($interval * $position);
                break;
        }

        return $payDate;
    }

    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}

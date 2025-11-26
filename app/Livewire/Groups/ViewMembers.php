<?php

namespace App\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Payment;
use Carbon\Carbon;

class ViewMembers extends Component
{
    public $group;
    public $membersOrder = [];
public $remainingAmount;
    public $memberToPay;    
    public $paymentAmount;  
    public $payerId;        
    public $showPaymentModal = false; 
    public $totalMembers;

    public function mount($group)
    {
        $this->group = Group::with('groupMembers.user')->findOrFail($group);

        foreach ($this->group->groupMembers as $member) {
            $this->membersOrder[$member->id] = $member->order_position;
        }

        $this->setNextMemberToPay();
    }

    


    /**
     * Hii inatafuta member wa sasa anayestahili kupokea malipo
     */
 

    public function setNextMemberToPay()
{
    $members = $this->group->groupMembers->sortBy('order_position');
    $totalMembers = $members->count();

    // Kiasi cha kuchangia kwa kila mtu (contribution_amount × interval)
    $singleMemberContribution = $this->group->contribution_amount * $this->group->interval;

    // Jumla anayopaswa kupokea member mmoja
    $payoutAmount = $singleMemberContribution * $totalMembers;

    foreach ($members as $member) {
        $paid = Payment::where('group_id', $this->group->id)
                    ->where('member_id', $member->id)
                    ->sum('amount');

        if ($paid < $payoutAmount) {
            $this->memberToPay = $member;
            return;
        }
    }

    $this->memberToPay = null;
}




    public function saveOrder()
    {
        $positions = array_values($this->membersOrder);

        if (in_array(null, $positions, true) || in_array('', $positions, true)) {
            session()->flash('error', 'Please fill all Order Positions before saving.');
            return;
        }

        if (count($positions) !== count(array_unique($positions))) {
            session()->flash('error', 'Each Order Position must be unique.');
            return;
        }

        $max = $this->group->groupMembers->count();
        foreach ($positions as $pos) {
            if ($pos < 1 || $pos > $max) {
                session()->flash('error', "Order positions must be between 1 and $max.");
                return;
            }
        }

        foreach ($this->membersOrder as $memberId => $order) {
            GroupMember::where('id', $memberId)->update(['order_position' => $order]);
        }

        session()->flash('success', 'Members order updated successfully!');
        $this->mount($this->group->id);
    }



public function openPaymentModal($memberId)
{
    // Ruhusu kulipa ni yule tu anayechangiwa sasa
    if (!$this->memberToPay || $this->memberToPay->id != $memberId) {
        session()->flash('error', 'This member is not eligible for payment yet.');
        return;
    }

    $totalMembers = $this->group->groupMembers->count();
    $perMemberAmount = $this->group->contribution_amount * $this->group->interval; // amount per payer
    $totalDue = $perMemberAmount * $totalMembers; // total to receive

    $alreadyPaid = Payment::where('group_id', $this->group->id)
                        ->where('member_id', $memberId)
                        ->sum('amount');

    $this->paymentAmount = max(0, $perMemberAmount); // each payer pays fixed amount
    $this->memberToPay = $this->group->groupMembers->find($memberId);

    $this->showPaymentModal = true;
}

public function processPayment()
{
    if(!$this->payerId || !$this->paymentAmount) {
        session()->flash('error', 'Select payer and amount.');
        return;
    }

    $totalMembers = $this->group->groupMembers->count();
    $perMemberAmount = $this->group->contribution_amount * $this->group->interval;

    // Check if this payer has already paid this member
    $alreadyPaid = Payment::where('group_id', $this->group->id)
                    ->where('member_id', $this->memberToPay->id)
                    ->where('payer_id', $this->payerId)
                    ->sum('amount');

    if($alreadyPaid >= $perMemberAmount){
        session()->flash('error', 'This member has already paid for this payout.');
        return;
    }

    // Save payment
    Payment::create([
        'group_id' => $this->group->id,
        'member_id' => $this->memberToPay->id,
        'payer_id' => $this->payerId,
        'amount' => $perMemberAmount,
        'paid_at' => now(),
    ]);

    session()->flash('success', 'Payment successful.');

    $currentMember = $this->memberToPay;

    $this->closePaymentModal();

    // Check if total paid by all members reached totalDue
    $totalPaid = Payment::where('group_id', $this->group->id)
                    ->where('member_id', $currentMember->id)
                    ->sum('amount');

    if($totalPaid >= $perMemberAmount * $totalMembers){
        // Move to next member
        $nextMember = $this->group->groupMembers()
            ->where('order_position', '>', $currentMember->order_position)
            ->orderBy('order_position', 'asc')
            ->first();

        if($nextMember){
            $this->memberToPay = $nextMember;
            $this->openPaymentModal($nextMember->id);
        } else {
            $this->memberToPay = null; // all done
        }
    }
}


















   public function closePaymentModal()
{
    $this->showPaymentModal = false;
    // USIWEKE HII 👉 $this->memberToPay = null;
    $this->paymentAmount = null;
    $this->payerId = null;
}





    /**
     * Schedule for display
     */
    
          

public function getPayoutSchedule()
{
    $schedule = [];
    $members = $this->group->groupMembers->sortBy('order_position');
    $totalMembers = $members->count();

    foreach ($members as $member) {
        $interval = $this->group->interval;
        $contribution = $this->group->contribution_amount;

        // Kiasi cha kulipwa kwa member huyu
        $amountDue = $contribution * $interval * $totalMembers;

        // Kiasi kilicholipwa tayari
        $amountPaid = Payment::where('group_id', $this->group->id)
                        ->where('member_id', $member->id)
                        ->sum('amount');

        $schedule[] = [
            'id' => $member->id,
            'order_position' => $member->order_position,
            'name' => $member->user->name ?? '-',
            'phone' => $member->user->phone ?? '-',
            'login_code' => $member->user->login_code ?? '-',
            'amount_due' => $amountDue,
            'amount_paid' => $amountPaid,
            'pay_date' => now()->format('Y-m-d'),
            'is_paid' => $amountPaid >= $amountDue,
        ];
    }

    return $schedule;
}




    public function render()
    {
        return view('livewire.groups.view-members');
    }
}

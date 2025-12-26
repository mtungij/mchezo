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

  public $search = '';
    public $filterStatus = 'all'; // <-- Declare this property


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
 






    public function saveOrder()
    {
        // Only group owner may change the order
        if (auth()->id() !== $this->group->owner_id) {
            session()->flash('error', 'Ni mmiliki tu anayeweza kubadilisha mpangilio.');
            return;
        }

        $positions = array_values($this->membersOrder);

        if (in_array(null, $positions, true) || in_array('', $positions, true)) {
            session()->flash('error', 'Tafadhali jaza nafasi zote kabla ya kuhifadhi.');
            return;
        }

        if (count($positions) !== count(array_unique($positions))) {
            session()->flash('error', 'Kila nafasi lazima iwe ya kipekee.');
            return;
        }

        $max = $this->group->groupMembers->count();
        foreach ($positions as $pos) {
            if ($pos < 1 || $pos > $max) {
                session()->flash('error', "Nafasi lazima ziwe kati ya 1 na $max.");
                return;
            }
        }

        // Prevent changing order for members who are already fully paid
        foreach ($this->membersOrder as $memberId => $order) {
            $member = GroupMember::find($memberId);
            if (! $member || $member->group_id !== $this->group->id) continue;

            $totalMembers = $this->group->groupMembers->count();
            $interval = $this->group->interval;
            $amountDue = $this->group->contribution_amount * $interval * $totalMembers;
            $amountPaid = Payment::where('group_id', $this->group->id)
                ->where('member_id', $member->id)
                ->sum('amount');

            if ($amountPaid >= $amountDue && $member->order_position != $order) {
                session()->flash('error', "Haiwezekani kubadilisha mpangilio wa {$member->user->name} - amelipwa kamili.");
                return;
            }
        }

        foreach ($this->membersOrder as $memberId => $order) {
            GroupMember::where('id', $memberId)->update(['order_position' => $order]);
        }

        session()->flash('success', 'Mpangilio umehifadhiwa.');
        $this->mount($this->group->id);
    }



public function openPaymentModal($memberId)
{
    $member = GroupMember::find($memberId);
    if (! $member || $member->group_id !== $this->group->id) {
        session()->flash('error', 'Mwanachama haipo.');
        return;
    }

    // Allow if owner OR if delegated to this member today
    $isOwner = auth()->id() === $this->group->owner_id;
    $isDelegatedMember = auth()->id() === $member->user_id && $member->can_pay && $member->can_pay_until && $member->can_pay_until->isSameDay(now());

    if (! $isOwner && ! $isDelegatedMember) {
        session()->flash('error', 'Ni mmiliki au aliyepewa ruhusa tu.');
        return;
    }

    // Ruhusu kulipa ni yule tu anayechangiwa sasa
    // Ensure this member is the one eligible for payments now
    if (!$this->memberToPay || $this->memberToPay->id != $memberId) {
        session()->flash('error', 'Mwanachama huyu bado hana sifa ya kupokea malipo.');
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
    // Only group owner or delegated member may process payments
    $member = GroupMember::find($this->memberToPay->id);
    $isOwner = auth()->id() === $this->group->owner_id;
    $isDelegatedMember = auth()->id() === $member->user_id && $member->can_pay && $member->can_pay_until && $member->can_pay_until->isSameDay(now());

    if (! $isOwner && ! $isDelegatedMember) {
        session()->flash('error', 'Ni mmiliki au aliyepewa ruhusa tu.');
        return;
    }

    $alreadyPaid = Payment::where('group_id', $this->group->id)
        ->where('member_id', $this->memberToPay->id)
        ->where('payer_id', $this->payerId)
        ->sum('amount');

    $remaining = $this->group->contribution_amount - $alreadyPaid;

    if ($this->paymentAmount > $remaining) {
        session()->flash('error', 'Huwezi kulipa zaidi ya kiasi kilichobaki.');
        return;
    }

    // Save payment
    Payment::create([
        'group_id' => $this->group->id,
        'member_id' => $this->memberToPay->id,
        'payer_id' => $this->payerId,
        'amount' => $this->paymentAmount,
        'paid_at' => now(),
    ]);

    // If delegated member processed payment, revoke their delegation for today
    if ($isDelegatedMember) {
        $member->update(['can_pay' => false, 'can_pay_until' => null]);
    }
    // ============================
    //  SMS NOTIFICATIONS LOGIC
    // ============================

    $receiver = $this->memberToPay->user;
    $payer = \App\Models\User::find($this->payerId);


    // dd($payer);

    $amount = number_format($this->paymentAmount, 0);
    $groupName = $this->group->name;


    // dd($groupName);

    // Total amount this receiver must get
    $totalMembers = $this->group->groupMembers->count();
    $totalDueForMember = $this->group->contribution_amount * $this->group->interval * $totalMembers;

    // Amount already paid to this receiver
    $amountPaidTotal = Payment::where('group_id', $this->group->id)
        ->where('member_id', $this->memberToPay->id)
        ->sum('amount');

    $remainingForMember = $totalDueForMember - $amountPaidTotal;
    $remainingFormatted = number_format($remainingForMember, 0);

    // ====== SMS kwa anayepokea ======
    // $messageReceiver = "Ndugu {$receiver->name}, umepokea malipo ya Tsh {$amount} kutoka kwa {$payer->name} kupitia kikundi cha {$groupName}.";

    // if ($remainingForMember > 0) {
    //     $messageReceiver .= " Bado Tsh {$remainingFormatted} kukamilisha malipo yako.";
    // } else {
    //     $messageReceiver .= " Hongera! Umekamilishiwa malipo yako yote.";
    // }

    // $this->sendsms($receiver->phone, $messageReceiver);

    // ====== SMS kwa anayelipa ======
    // $messagePayer = "Ndugu {$payer->name}, umelipa Tsh {$amount} kwa {$receiver->name} kupitia kikundi cha {$groupName}.";

    // if ($remainingForMember > 0) {
    //     $messagePayer .= " Bado {$receiver->name} anahitaji Tsh {$remainingFormatted} ili kumaliza malipo.";
    // }

    // $this->sendsms($payer->phone, $messagePayer);

    // ====== SMS kwa Group Admin ======
    // Chagua admin hapa:
    $adminPhone = $this->group->admin->phone ?? null; // kama una admin relation
    // au: $adminPhone = '255xxxxxxxxx';

    if ($adminPhone) {
        $messageAdmin = "Payment Alert: {$payer->name} amelipa Tsh {$amount} kwa {$receiver->name} katika kikundi cha {$groupName}.";
        
        if ($remainingForMember > 0) {
            $messageAdmin .= " Bado Tsh {$remainingFormatted} kumaliza malipo ya {$receiver->name}.";
        } else {
            $messageAdmin .= " {$receiver->name} amekamilishiwa malipo yote.";
        }

        $this->sendsms($adminPhone, $messageAdmin);
    }

    // ====== SMS kwa anayepokea ======
    if ($receiver && $receiver->phone) {
        $messageReceiver = "Ndugu {$receiver->name}, umepokea malipo ya Tsh {$amount} kutoka kwa {$payer->name} kupitia kikundi cha {$groupName}.";

        if ($remainingForMember > 0) {
            $messageReceiver .= " Bado Tsh {$remainingFormatted} kukamilisha malipo yako.";
        } else {
            $messageReceiver .= " Hongera! Umekamilishiwa malipo yako yote.";
        }

        try {
            $this->sendsms($this->formatPhoneForSms($receiver->phone), $messageReceiver);
        } catch (\Throwable $e) {
            // ignore SMS errors
        }
    }

    // ====== SMS kwa anayelipa ======
    if ($payer && $payer->phone) {
        $messagePayer = "Ndugu {$payer->name}, umelipa Tsh {$amount} kwa {$receiver->name} katika kikundi cha {$groupName}.";

        if ($remainingForMember > 0) {
            $messagePayer .= " Bado {$receiver->name} anahitaji Tsh {$remainingFormatted} ili kumaliza malipo.";
        }

        try {
            $this->sendsms($this->formatPhoneForSms($payer->phone), $messagePayer);
        } catch (\Throwable $e) {
            // ignore SMS errors
        }
    }

    session()->flash('success', 'Malipo yamehifadhiwa.');

    $this->reset('paymentAmount', 'payerId');
    $this->setNextMemberToPay();
}




public function setNextMemberToPay()
{
    $members = $this->group->groupMembers->sortBy('order_position');
    $totalMembers = $members->count();
    $perMemberAmount = $this->group->contribution_amount * $this->group->interval;
    $totalDue = $perMemberAmount * $totalMembers;

    foreach ($members as $member) {
        $totalPaidByAll = Payment::where('group_id', $this->group->id)
            ->where('member_id', $member->id)
            ->sum('amount');

        if ($totalPaidByAll < $totalDue) {
            // Check if member has contributed his own share
            $selfPaid = Payment::where('group_id', $this->group->id)
                ->where('member_id', $member->id)
                ->where('payer_id', $member->user_id)
                ->sum('amount');

            if ($selfPaid < $perMemberAmount) {
                // Member lazima achangie kwanza
                $this->memberToPay = $member;
                return;
            }

            // Next member eligible for payments from others
            $this->memberToPay = $member;
            return;
        }
    }

    $this->memberToPay = null; // All done
}


 public function sendsms($phone,$massage){
    //public function sendsms(){f
    //$phone = '255628323760';
    //$massage = 'mapenzi yanauwa';
    // $api_key = '';                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
    //$api_key = 'qFzd89PXu1e/DuwbwxOE5uUBn6';
    //$curl = curl_init();
    $url = "https://sms-api.kadolab.com/api/send-sms";
    $token = getenv('SMS_TOKEN');

  
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Authorization: Bearer '. $token,
      'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
      "phoneNumbers" => ["$phone"],
      "message" => $massage
    ]));
  
  $server_output = curl_exec($ch);
  curl_close ($ch);
  
  //print_r($server_output);
  }

  /**
   * Convert phone into local SMS-friendly form (e.g., 2557xxxx -> 07xxxx)
   */
  private function formatPhoneForSms($phone)
  {
      if (! $phone) return null;
      $p = preg_replace('/[^0-9+]/', '', $phone);
      $p = ltrim($p, '+');

      if (str_starts_with($p, '255') && strlen($p) > 3) {
          return '0' . substr($p, 3);
      }

      if (str_starts_with($p, '0')) {
          return $p;
      }

      if (strlen($p) === 9) {
          return '0' . $p;
      }

      return $p;
  }
  

// public function prcessPayment()
// {
//     if(!$this->payerId || !$this->paymentAmount) {
//         session()->flash('error', 'Select payer and amount.');
//         return;
//     }

//     $perMemberAmount = $this->group->contribution_amount * $this->group->interval;
//     $totalMembers = $this->group->groupMembers->count();

//     $member = $this->memberToPay;

//     // Ensure payer is allowed
//     if ($this->payerId != $member->user_id) {
//         // Payer can pay only if previous member fully paid
//         $prevMember = $this->group->groupMembers()
//             ->where('order_position', '<', $member->order_position)
//             ->orderBy('order_position', 'desc')
//             ->first();

//         if ($prevMember) {
//             $prevPaid = Payment::where('group_id', $this->group->id)
//                 ->where('member_id', $prevMember->id)
//                 ->sum('amount');

//             $prevDue = $perMemberAmount * $totalMembers;
//             if ($prevPaid < $prevDue) {
//                 session()->flash('error', 'Cannot pay this member until previous member is fully paid.');
//                 return;
//             }
//         }
//     }

//     // Check if payer has already paid this member
//     $alreadyPaid = Payment::where('group_id', $this->group->id)
//                     ->where('member_id', $member->id)
//                     ->where('payer_id', $this->payerId)
//                     ->sum('amount');

//     if($alreadyPaid >= $perMemberAmount){
//         session()->flash('error', 'This member has already received full payment from this payer.');
//         return;
//     }

//     Payment::create([
//         'group_id' => $this->group->id,
//         'member_id' => $member->id,
//         'payer_id' => $this->payerId,
//         'amount' => $perMemberAmount,
//         'paid_at' => now(),
//     ]);

//     session()->flash('success', 'Payment successful.');

//     $this->closePaymentModal();
//     $this->setNextMemberToPay();
// }



















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

        foreach ($members as $member) {
            $startDate = \Carbon\Carbon::parse($this->group->start_date);
            $interval = $this->group->interval;

            switch ($this->group->frequency_type) {
                case 'day':
                    $payDate = $startDate->copy()->addDays($interval * $member->order_position);
                    break;
                case 'week':
                    $payDate = $startDate->copy()->addWeeks($interval * $member->order_position);
                    break;
                case 'month':
                    $payDate = $startDate->copy()->addMonths($interval * $member->order_position);
                    break;
                default:
                    $payDate = $startDate->copy();
            }

            $totalMembers = $this->group->groupMembers->count();
            $amount = $this->group->contribution_amount * $interval * $totalMembers;

            $amountPaid = Payment::where('group_id', $this->group->id)
                ->where('member_id', $member->id)
                ->sum('amount');

            $isPaid = $amountPaid >= $amount;

            $schedule[] = [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'order_position' => $member->order_position,
                'name' => $member->user->name ?? '-',
                'phone' => $member->user->phone ?? '-',
                'passport' => $member->user->passport ?? null,
                'login_code' => $member->user->login_code ?? '-',
                'pay_date' => $payDate->format('Y-m-d'),
                'amount_due' => $amount,
                'amount_paid' => $amountPaid,
                'is_paid' => $isPaid,
                'can_pay' => (bool) $member->can_pay,
                'can_pay_until' => $member->can_pay_until ? $member->can_pay_until->format('Y-m-d') : null,
            ];
        }

        // 🔍 Search only
        if (!empty($this->search)) {
            $schedule = array_filter($schedule, function ($item) {
                return str_contains(strtolower($item['name']), strtolower($this->search))
                    || str_contains($item['phone'], $this->search)
                    || str_contains($item['login_code'], $this->search);
            });
        }
            return $schedule;
}

public function getMembersStatsProperty()
{
    $schedule = $this->getPayoutSchedule();
    $total = count($schedule);
    $paid = collect($schedule)->where('is_paid', true)->count();
    $notPaid = max(0, $total - $paid);
    $endDate = null;
    if ($total > 0) {
        $dates = collect($schedule)->pluck('pay_date')->filter()->all();
        if (! empty($dates)) {
            $endDate = collect($dates)->max();
        }
    }

    return [
        'total' => $total,
        'paid' => $paid,
        'not_paid' => $notPaid,
        'end_date' => $endDate,
    ];
}

public function togglePaymentRights($memberId)
    {
        // Only owner can toggle
        if (auth()->id() !== $this->group->owner_id) {
            session()->flash('error', 'Ni mmiliki tu.');
            return;
        }

        $member = GroupMember::find($memberId);
        if (! $member || $member->group_id !== $this->group->id) {
            session()->flash('error', 'Mwanachama haipo.');
            return;
        }

        // compute pay date for member
        $startDate = \Carbon\Carbon::parse($this->group->start_date);
        $interval = $this->group->interval;
        switch ($this->group->frequency_type) {
            case 'day':
                $payDate = $startDate->copy()->addDays($interval * $member->order_position);
                break;
            case 'week':
                $payDate = $startDate->copy()->addWeeks($interval * $member->order_position);
                break;
            case 'month':
                $payDate = $startDate->copy()->addMonths($interval * $member->order_position);
                break;
            default:
                $payDate = $startDate->copy();
        }

        if (! $payDate->isSameDay(now())) {
            session()->flash('error', 'Ruhusa inatolewa tu kwa leo.');
            return;
        }

        // Toggle
        if ($member->can_pay && $member->can_pay_until && $member->can_pay_until->isSameDay(now())) {
            $member->update(['can_pay' => false, 'can_pay_until' => null]);
            session()->flash('success', 'Ruhusa imeondolewa.');
        } else {
            $member->update(['can_pay' => true, 'can_pay_until' => now()->toDateString()]);
            session()->flash('success', 'Ruhusa imetolewa.');
        }

        $this->mount($this->group->id);
}

public function revokePaymentRights($memberId)
{
    if (auth()->id() !== $this->group->owner_id) {
        session()->flash('error', 'Ni mmiliki tu.');
        return;
    }

    $member = GroupMember::find($memberId);
    if (! $member || $member->group_id !== $this->group->id) {
        session()->flash('error', 'Mwanachama haipo.');
        return;
    }

    $member->update(['can_pay' => false, 'can_pay_until' => null]);

    session()->flash('success', 'Ruhusa imeondolewa.');
}




    public function render()
    {
        return view('livewire.groups.view-members');
    }
}

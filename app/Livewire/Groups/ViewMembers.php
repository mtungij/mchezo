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

    // dd(   $totalDue );

    $this->showPaymentModal = true;
}

public function processPayment()
{
    $alreadyPaid = Payment::where('group_id', $this->group->id)
        ->where('member_id', $this->memberToPay->id)
        ->where('payer_id', $this->payerId)
        ->sum('amount');

    $remaining = $this->group->contribution_amount - $alreadyPaid;

    if ($this->paymentAmount > $remaining) {
        session()->flash('error', 'You cannot pay more than the remaining amount.');
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

    // ============================
    //  SMS NOTIFICATIONS LOGIC
    // ============================

    $receiver = $this->memberToPay->user;
    $payer = \App\Models\User::find($this->payerId);


    // dd($payer);

    $amount = number_format($this->paymentAmount, 0);
    $groupName = $this->group->name;


    dd($groupName);

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
    $messageReceiver = "Ndugu {$receiver->name}, umepokea malipo ya Tsh {$amount} kutoka kwa {$payer->name} kupitia kikundi cha {$groupName}.";

    if ($remainingForMember > 0) {
        $messageReceiver .= " Bado Tsh {$remainingFormatted} kukamilisha malipo yako.";
    } else {
        $messageReceiver .= " Hongera! Umekamilishiwa malipo yako yote.";
    }

    $this->sendsms($receiver->phone, $messageReceiver);

    // ====== SMS kwa anayelipa ======
    $messagePayer = "Ndugu {$payer->name}, umelipa Tsh {$amount} kwa {$receiver->name} kupitia kikundi cha {$groupName}.";

    if ($remainingForMember > 0) {
        $messagePayer .= " Bado {$receiver->name} anahitaji Tsh {$remainingFormatted} ili kumaliza malipo.";
    }

    $this->sendsms($payer->phone, $messagePayer);

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

    session()->flash('success', 'Payment recorded successfully.');

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

        // Hesabu payout date
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




        // Kiasi kilicholipwa tayari
        $amountPaid = Payment::where('group_id', $this->group->id)
                        ->where('member_id', $member->id)
                        ->sum('amount');

        // Check if member is already fully paid
        $isPaid = \App\Models\Payment::where('group_id', $this->group->id)
                    ->where('member_id', $member->id)
                    ->sum('amount') >= $amount;

        $schedule[] = [
            'id' => $member->id,
            'order_position' => $member->order_position,
            'name' => $member->user->name ?? '-',
            'phone' => $member->user->phone ?? '-',
            'passport' => $member->user->passport ?? null,
            'login_code' => $member->user->login_code ?? '-',
            'pay_date' => $payDate->format('Y-m-d'),
            'amount_due' => $amount,
            'amount_paid'=>$amountPaid,
            'is_paid' => $isPaid,
        ];
    }

    return $schedule;
}





    public function render()
    {
        return view('livewire.groups.view-members');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\GroupMember;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterInviteController extends Controller
{
    public function index($code)
    {
        $group = Group::with('members')->where('invite_code', $code)->firstOrFail();
        return view("livewire.groups.register-through-invite", [
            'invite_code' => $code,
            'group' => $group,
        ]);
    }

    public function showGroupMemberRegistered($code)
    {
        $group = Group::with('members')->where('invite_code', $code)->firstOrFail();
        $user = auth()->user();

        return view("group-member-registered", [
            'group' => $group,
            'user' => $user,
        ]);
    }

    
public function register(Request $request)
{
   $validated = $request->validate([
        'name' => 'required|string|min:3',
        'phone' => 'required|string|min:9|unique:users,phone',
        'passport' => 'nullable|image', // max 1MB
    ]);

    $group = Group::with('members')->where('invite_code', request()->input('code'))->firstOrFail();

    $user_logincode = null;

    DB::transaction(function () use ($validated, $group, &$passportPath, $request, &$user_logincode) {
        // Format phone
        $formattedPhone = $this->formatPhone($validated["phone"]);
    
        $user = User::create([
            'name' => $validated['name'],
            // store formatted phone (255...), but we'll send the local phone in SMS
            'phone' => $formattedPhone,
            'email' => $formattedPhone.'@example.com',
            'role'=>"member",
            'passport' => $passportPath ?? null,
            'password' => Hash::make(Str::random(8)),
            'login_code' => strtoupper(Str::random(4)),
        ]);

        $user_logincode = $user->login_code;
    
        $order = $group->members()->count() + 1;
    
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'order_position' => $order,
        ]);
        // Use local format (starting with 0) for SMS readability
        $localPhone = preg_replace('/^255/', '0', $user->phone);

        $massage = "Ndugu {$user->name}, umejisajili kwenye kikundi cha {$group->name}. "
            . "Code yako ya kuingia ni: {$user->login_code}. "
            . "Kiasi cha kuchangia ni Tsh " . number_format($group->contribution_amount) . ". "
            . "Itunze code yako usishare na mtu yoyote.";
    
             
    
        // Send SMS
        $this->sendsms($localPhone, $massage);

        Auth::login($user);

        $request->session()->regenerate();

        session()->flash('success', "Umejisajili kikamilifu. Login code: $user_logincode, PHONE: $localPhone");
    });
 

    return redirect()->route('group.member', ['code' => $group->invite_code]);

}

    private function formatPhone($phone)
{
    // Trim and remove non-digits
    $phone = preg_replace('/\D+/', '', trim($phone));

    // If it starts with 0 (0712...), convert to 2557...
    if (Str::startsWith($phone, '0')) {
        return '255' . substr($phone, 1);
    }

    // If it already starts with 255, keep as-is
    if (Str::startsWith($phone, '255')) {
        return $phone;
    }

    // Otherwise assume it's missing leading 255 and prefix it
    return '255' . $phone;
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
      "phoneNumbers" => ["+$phone"],
      "message" => $massage
    ]));
  
  $server_output = curl_exec($ch);
  curl_close ($ch);
  
  //print_r($server_output);
  }
}

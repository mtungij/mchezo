<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use App\Models\GroupMember;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;


class RegisterThroughInvite extends Component
{

    use WithFileUploads;
    public $group;
    public $name;
    public $phone;

       public $passport;

    public function mount($code)
    {
        // Find group by invite code
        $this->group = Group::where('invite_code', $code)->firstOrFail();

        // dd($this->group);
    }

    protected $rules = [
        'name' => 'required|string|min:3',
        'phone' => 'required|string|min:9',
         'passport' => 'required|image|max:1024', // max 1MB
    ];

 public function register()
{
    $this->validate();

    // Format phone
    $formattedPhone = $this->formatPhone($this->phone);

    $passportPath = $this->passport->store('passports', 'public');

    $user = User::create([
        'name' => $this->name,
        'phone' => $formattedPhone,
        'email' => $formattedPhone.'@example.com',
        'passport' => $passportPath,
        'password' => Hash::make(Str::random(8)),
        'login_code' => strtoupper(Str::random(4)),
    ]);

    $order = $this->group->members()->count() + 1;

    GroupMember::create([
        'group_id' => $this->group->id,
        'user_id' => $user->id,
        'order_position' => $order,
    ]);
$phone = $user->phone;
     $massage = "Ndugu {$user->name}, umejisajili kwenye kikundi cha {$this->group->name}. "
         . "Code yako ya kuingia ni: {$user->login_code}. "
         . "Kiasi cha mchango ni Tsh {$this->group->contribution_amount}. "
         . "Malipo yanapaswa kufanyika kabla ya tarehe {$this->group->payment_due_date}. "
         . "Ingia kwenye mfumo kupitia {$this->group->login_url}. "
         . "Itunze code yako usishare na mtu yoyote.";


    // Send SMS
    $this->sendsms($phone, $massage);

    session()->flash('success', "Registration successful! Your login code: ".$user->login_code);

    $this->name = '';
    $this->phone = '';
}



    private function formatPhone($phone)
{
    // Ondoa whitespaces
    $phone = trim($phone);

    // Kama inaanza na +255, rudisha bila kubadili
    if (Str::startsWith($phone, '+255')) {
        return $phone;
    }

    // Kama inaanza na 0 (mfano 0712...)
    if (Str::startsWith($phone, '0')) {
        return '+255' . substr($phone, 1);
    }

    // Kama mwanzo si 0 wala +255, assume ni 7xxxx au 6xxxx
    return '+255' . $phone;
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
  

    public function render()
    {
        return view('livewire.groups.register-through-invite');
    }
}
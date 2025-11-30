<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
       Validator::make($input, [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique(User::class),
        ],
        // Lazima ianze na 06 au 07, na iwe tarakimu 10
        'phone' => [
            'required',
            'regex:/^(06|07)[0-9]{8}$/',
            Rule::unique(User::class),
        ],
        'password' => $this->passwordRules(),
    ],
    [
        'phone.regex' => 'Namba ya simu lazima ianze na 06 au 07 na iwe na tarakimu 10.',
    ])->validate();

    // Badilisha namba kuwa +255xxxxxxxxx
    $phone = $input['phone'];

    if (substr($phone, 0, 1) === '0') {
        $phone = '+255' . substr($phone, 1);
    }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'phone'=> $input['phone'],
            'role' => 'admin',
        ]);
    }

    
}

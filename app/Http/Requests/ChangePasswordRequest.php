<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends BaseRequest
{
    
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:users,id',
            'current_password' => 'required',
            'password'          => ['bail','required', 'confirmed', 'max:60', Password::min(8)
                                                    ->letters()
                                                    ->mixedCase()
                                                    ->numbers()
                                                    ->symbols()
                                                        ]
            
        ];
    }


    public function withValidator($validator)
    {
        $user = User::find($this->id);
        if($user)
        {
            $validator->after(function ($validator)  use($user){
                if ( !Hash::check($this->current_password, $user->password) ) {
                    $validator->errors()->add('current_password', 'Your current password is incorrect.');
                }
            });
        }
        
        return;
    }
}

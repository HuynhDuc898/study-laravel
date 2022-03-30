<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    
    public function rules()
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,'.$this->id,
            'role_id' => 'required|integer|exists:roles,id',
            'id'    => 'required|integer|exists:users,id'

        ];
    }
}

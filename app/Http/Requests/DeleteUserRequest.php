<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class DeleteUserRequest extends BaseRequest
{
    
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:users,id'
        ];
    }
}

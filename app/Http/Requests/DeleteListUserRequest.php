<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class DeleteListUserRequest extends BaseRequest
{
    
    public function rules()
    {
        return [
            'id' => 'array',
            'id.*' => 'required|integer|exists:users,id'
        ];
    }
}

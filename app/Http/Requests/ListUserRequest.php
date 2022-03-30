<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class ListUserRequest extends BaseRequest
{
    
    public function rules()
    {
        return [
            'search' => 'nullable|max:255'
        ];
    }
}

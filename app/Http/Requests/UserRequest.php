<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email|max:255',
            'password' => ['bail','required', 'confirmed', 'max:60', Password::min(8)
                                                    ->letters()
                                                    ->mixedCase()
                                                    ->numbers()
                                                    ->symbols()
        ],
            'role_id' => 'required|integer|exists:roles,id',
            'options' => 'nullable',
            'avatar'    => 'nullable|mimes:jpg,jpeg,png'
        ];
    }

    public function messages()
    {
        return [
            // 'name.required' => 'abcd',
            // 'email.required' => 'abcdf'
        ];
    }
}

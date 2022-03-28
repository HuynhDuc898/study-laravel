<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function create(UserRequest $request)
    {
        
        $data  = $request->safe()->only([
            'name',
            'email',
            'password'
        ]);

       $data['password'] = bcrypt($data['password']);

        //----create user---------
        $result = User::create($data);
        
        return json_encode($result);
    }

    public function update()
    {
        $result = User::where('id', 1)->update(['email' => 'huynhtest1@gmail.com']);
        return json_encode($result);
    }

    public function list()
    {
        $result = User::get();
        return json_encode($result);
    }

    public function delete()
    {
        $result = User::where('id', 1)->delete();
        return [];
    }
}

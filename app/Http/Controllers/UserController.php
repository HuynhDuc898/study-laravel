<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Mail\RegisterUser;
use App\Mail\ChangePasswordUser;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    //------create use-----------------
    public function create(UserRequest $request)
    {
        
        $data  = $request->safe()->only([
            'name',
            'email',
            'password'
        ]);

       $data['password'] = $this->setPasswordAttribute($data['password']);
        //----create user---------
        $result = User::create($data);

        //-----send mail------------
        // Mail::to($result->email)->send(new RegisterUser($result));
        return json_encode($result);
    }

    //-----hash password--------------
    public function setPasswordAttribute($password)
    {
        $result = $password;

        if($password != null && $password != "")
        {
            //--------hash password--------
            $result = bcrypt($password);
        }

        return $result;
    }

    //--------change password--------
    public function changePassword(ChangePasswordRequest $request)
    {
        $data  = $request->safe()->only([
            'id',
            'password'
        ]);
        $data['password'] = $this->setPasswordAttribute($data['password']);
        $user = User::find($data['id']);
        
        if($user)
        {
            $result = $user->update(['password' => $data['password']]);
        }
        
        //-----send mail------------
        // Mail::to($result->email)->queue(new ChangePasswordUser($result));

        return json_encode($user);
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

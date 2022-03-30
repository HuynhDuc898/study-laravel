<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

use App\Models\User;

use App\Http\Requests\UserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DeleteUserRequest;

use App\Mail\RegisterUser;
use App\Mail\ChangePasswordUser;


class UserController extends Controller
{

    //------create use-----------------
    public function create(UserRequest $request)
    {
        
        $data  = $request->safe()->only([
            'name',
            'email',
            'password',
            'role_id',
        ]);

        $data['password'] = $this->setPasswordAttribute($data['password']);
        //----create user---------
        $result = User::create($data);

        //-----send mail------------
        // Mail::to($result->email)->send(new RegisterUser($result));
        return response()->json($result, 200);
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

        return response()->json($user, 200); 
    }

    //---------login--------------
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            if (! $token = JWTAuth::attempt($credentials)) {

                return response()->json(['error' => 'invalid_credentials'], 400);

            }

        } catch (JWTException $e) {

            return response()->json(['error' => 'could_not_create_token'], 500);

        }

        return response()->json(compact('token'),200);
    }


    //---------soft delete----------
    public function softDelete(DeleteUserRequest $request)
    {
        $data = $request->safe()->only([
            'id'
        ]);
        $result = User::find($data['id']);
        if($result)
        {
            $result->delete();
        
            return response()->json(['soft delete success'], 200);
        }
        
        return response()->json(['not found user'], 400);
    }

    //----------restore---------------
    public function restore(DeleteUserRequest $request)
    {
        $data = $request->safe()->only([
            'id'
        ]);
        $result = User::withTrashed()->find($data['id']);
       
        if($result->trashed())
        {
            $result->restore();
        
            return response()->json(['restore user'], 200);
        }
        
        return response()->json(['not found user soft delete'], 400);
    }


    //--------------Permanently Delete---------------
    public function delete(DeleteUserRequest $request)
    {
        $data = $request->safe()->only([
            'id'
        ]);
        $result = User::withTrashed()->find($data['id']);
       
        if($result->trashed())
        {
            $result->forceDelete();
        
            return response()->json(['delete user'], 200);
        }

        
        return response()->json(['not found user'], 200);
    }

    public function update()
    {
        $result = User::where('id', 1)->update(['email' => 'huynhtest1@gmail.com']);
        return json_encode($result);
    }

    //---------------list user not soft delete-------------------
    public function list()
    {
        $result = User::with('role')->get();
        return json_encode($result, 200);
    }

    //-----------------list all user-----------------------
    public function listAll()
    {
        $result = User::withTrashed()->with('role')->get();
        return json_encode($result, 200);
    }

    public function listSoftDelete()
    {
        $result = User::onlyTrashed()->with('role')->get();
        return json_encode($result, 200);
    }

    
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Log;

use App\Http\Requests\UserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DeleteUserRequest; 
use App\Http\Requests\UpdateUserRequest; 
use App\Http\Requests\DeleteListUserRequest; 
use App\Http\Requests\ListUserRequest; 

use App\Mail\RegisterUser;
use App\Mail\ChangePasswordUser;


class UserController extends Controller
{

    //------create use-----------------
    public function create(UserRequest $request)
    {
        //safe phải có xác thực trong request mới lấy được
        $data  = $request->safe()->only([
            'first_name',
            'last_name',
            'email',
            'password',
            'role_id',
            'options'
        ]);

        $data['password'] = $this->setPasswordAttribute($data['password']);
        // $data['name'] = $data['first_name'].' '.$data['last_name'];
        //----create user---------
        $result = User::create($data);
        // $token = JWTAuth::fromUser($result);​
        //-----send mail------------
        // Mail::to($result->email)->send(new RegisterUser($result));
        
        // return response()->json(compact('result', 'token'), 200);
        return response()->json(compact('result'), 200);
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
            
            Log::create([
                'user_id'   => $user->id,
                'action'    => 'change_password'
            ]);
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
        
        $user = Auth::user();
        if($user)
        {
            Log::create([
                'user_id'   => $user->id,
                'action'    => 'login'
            ]);
        }
        
        return response()->json(compact('token'),200);
    }

    //--------------delete restore----------------
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

    //------------list delete permanently---------------
    public function deleteList(DeleteListUserRequest $request)
    {
        $data = $request->safe()->only([
            'id'
        ]);
        $result = User::withTrashed()->whereIn('id',$data['id'])->forceDelete();
        
        
        return response()->json(['success'],200);
    }

    //-----------list soft delete-------------------
    public function softDeleteList(DeleteListUserRequest $request)
    {
        $data = $request->safe()->only([
            'id'
        ]);
        $result = User::whereIn('id',$data['id'])->delete();
        
        
        return response()->json(['success'],200);
    }

    //-------end delele and restore-------------

    //------------update user--------
    public function update(UpdateUserRequest $request)
    {
        $data  = $request->safe()->only([
            'first_name',
            'last_name',
            'email',
            'role_id',
            'id'
        ]);
        $result = User::find($data['id']);
        
        $result->update($data);
        return response()->json([$result], 200);
    }

    //-----------------list---------------------------
    //---------------list user not soft delete-------------------
    public function list(ListUserRequest $request)
    {
        $data = $request->safe()->only([
            'search'
        ]);
        $result = User::query();
        isset($data['search']) ? $result->where('name',"LIKE", "%".$data['search']."%") : '';
        $result = $result->with('role')->get();

        return response()->json([$result], 200);
    }

    //-----------------list all user-----------------------
    public function listAll()
    {
        $result = User::withTrashed()->with('role')->get();
        return response()->json([$result], 200);
    }

    //----------list soft delete--------------
    public function listSoftDelete()
    {
        $result = User::onlyTrashed()->with('role')->get();
        return response()->json([$result], 200);
    }

    //-------end list-----------------


    //-----detail user--------------
    public function detail($id = null)
    {
        $result = User::find($id);

        if($result)
        {
            // $options = $result->options;
            // die(json_encode($options));
            return response()->json([$result], 200);
        }
        
        return response()->json([], 200);
    }



    
}

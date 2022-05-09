<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

use App\Models\User;
use App\Models\Role;
use App\Models\Log;
use App\Models\Image;
use App\Models\Acticle;
use DB;

use App\Http\Requests\UserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\DeleteUserRequest; 
use App\Http\Requests\UpdateUserRequest; 
use App\Http\Requests\DeleteListUserRequest; 
use App\Http\Requests\ListUserRequest; 
use App\Http\Requests\DeleteListSearchUserRequest; 

use App\Mail\RegisterUser;
use App\Mail\ChangePasswordUser;

use App\Jobs\InsertUser;
use App\Jobs\InsertUserOptimize;

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
            'options',
            'avatar'
        ]);
        $data['password'] = $this->setPasswordAttribute($data['password']);
        // $data['name'] = $data['first_name'].' '.$data['last_name'];

        //----------insert avatar-------------
        // if($request->has('avatar'))
        // {
        //     $path = Storage::disk('public')
        //             ->putFile('avatars', $request->file('avatar'));
        //     $image = Image::create([
        //         'path' => 'storage/'.$path,
        //         'type' => 'avatar'
        //     ]);
        //     $data['avatar_id'] = $image->id;
        // }

        //----create user---------
        $result = User::create($data);
        // $token = JWTAuth::fromUser($result);​
        //-----send mail------------
        // Mail::to($result->email)->send(new RegisterUser($result));
        
        // return response()->json(compact('result', 'token'), 200);
        
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'body'  => $result
        ], 200);
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

    //---------list soft delete search----------------
    public function softDeleteSearch(ListUserRequest $request)
    {
        $data = $request->safe()->only([
            'search'
        ]);
        $result = User::query();
        isset($data['search']) ? $result->where('name',"LIKE", "%".$data['search']."%") : '';
        $result = $result->delete();

        return response()->json([$result], 200);
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
        $result = $result->with('role')->limit(20)->get();
        return response()->json($result, 200);
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
        return response()->json($result, 200);
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

    public function dashboard(Request $request)
    {
        $countUser = User::count();
        $result = [
            'user' => $countUser
        ];
        return response()->json($result, 200);
    }

    //-------------test query------------
    public function testQuery(Request $request)
    {

        // microtime()
        $time_start = microtime(true);
        $data = $request->only('type');
        switch ($data['type']) {
            case 1:
                    //-------------n+1 query--------------
                    // $result = User::select('id')->limit(100)->get();
                    $result = User::select(['id', 'role_id'])->limit(10000)->get();
                    foreach ($result as $key => $value) {
                        $value['name_role'] = Role::where('id', $value['role_id'])->first()->name;
                    }
                    $count = $result->count();
                break;
            case 2:
                
                    // --------------join-----------------
                    $result = User::addSelect(['name_role_1' => Role::select('name as name_role')
                    ->whereColumn('id', 'users.role_id')
                    ->limit(1)
                    ])
                                    // ->select(['users.*', 'roles.name as name_role'])
                                    ->limit(10000)
                                    ->get();
                    
                    // $result = DB::table('users')->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                    // ->select(['users.id', 'users.role_id','roles.name as name_role'])
                    // // ->limit(100000)
                    // ->get();
                    $count = $result->count();
                break;
            case 3:
                   

                    $result = User::
                    limit(50)
                    ->get();
                    foreach ($result as $key => $value) {
                        $value->acticles->sortByDesc('created_at')->first();
                    }

                    // $count = $result->count();
                break;
                case 4:
                    $result = User::
            
                    with('latest_acticle')
                    ->limit(50)
                    ->get();
                    // $count = $result->count();
                    break;
                case 5:
                    $result = User::
                    withLatestComment()
                    ->limit(50)
                    ->get();  

                    // $count = $result->count();
                    break;
            default:
                $result = [];
                break;
        }
        
        
        
        echo($result);
        // return response()->json([$reponse], 200);
    }


    //---------------test queue---------------
    public function testQueue(Request $request)
    {
        $data = $request->only('type');
        switch ($data['type']) {
            case 1:
                $result = new InsertUser();
                dispatch($result);
                break;
            case 2:
                $result = new InsertUserOptimize();
                dispatch($result);
                break;
            default:
                # code...
                break;
        }
        return response()->json([], 200);
    }



    
}

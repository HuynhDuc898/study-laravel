<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function create()
    {
        $result = User::create(['name' => 'Huynh', 'email' => 'huynhtest@gmail.com', 'password' => bcrypt('123456')]);
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

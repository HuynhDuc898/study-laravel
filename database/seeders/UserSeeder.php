<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        for ($i=0; $i < 1000 ; $i++) { 
            // $data[] = [
            //     'name' => Str::random(10),
            //     'email' => Str::random(10).'_'.uniqid().'@gmail.com',
            //     'password' => Hash::make('12345678aA@'),
            //     'role_id' => 1
            // ];

            $data[] = [
                'name' => Str::random(10),
                'content' => Str::random(10),
                'writter_id' => rand(1,100),
            ];
        }
        
        // DB::table('users')->insert($data);
        DB::table('acticles')->insert($data);
    }
}

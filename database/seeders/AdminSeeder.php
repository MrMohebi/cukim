<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Mail\Transport\SesTransport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = now();
        DB::table('admins')->insert([
           "username"=>Str::random(5),
            "password"=> Hash::make('123456789'),
            "email"=>Str::random(5)."gmail.com",
            "name"=>"Mr ".Str::random(3),
            "position"=>"moderator",
            "phone"=>"09031232569",
            "token"=>Str::random(),
            "promoted_by"=>Str::random(5),
            "last_login"=>$timestamp,
            "created_at"=>$timestamp,
            "updated_at"=>$timestamp,
        ]);
    }
}

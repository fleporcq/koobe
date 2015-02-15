<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('rates')->delete();
        DB::table('downloads')->delete();
        DB::table('users')->delete();

        User::create(array(
           "email" => "fleporcq@gmail.com",
           "password" => Hash::make('fleporcq')
        ));
    }
}
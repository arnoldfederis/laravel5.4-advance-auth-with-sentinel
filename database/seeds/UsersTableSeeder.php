<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'username'       => 'admin',
            'email'          => 'admin@admin.com',
            'password'       => 'admin',
            'first_name'     => 'Arnold',
            'last_name'      => 'Federis'
        ];

        $user = Sentinel::registerAndActivate($data);

        $user->permissions = [
            'admin' => true,
            'normal_user' => true,
            'can_login_admin' => true
        ];

        $user->roles()->sync([1,2]);

        $user->save();
    }
}

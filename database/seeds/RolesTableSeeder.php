<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$roles = [
    		[
    			'slug' => 'admin',
	            'name' => 'Administrator',
	            'permissions' => '{"admin":true,"normal_user":true,"can_login_admin":true}',
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now()
        	],
        	[
    			'slug' => 'normal_user',
	            'name' => 'Normal User',
	            'permissions' => '{"normal_user":true}',
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now()
        	],
    	];
        
        DB::table('roles')->insert($roles);
    }
}

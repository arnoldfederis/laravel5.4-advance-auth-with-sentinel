<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
        	[
        		'slug' => 'admin',
        		'name' => 'Administrator',
        		'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now()
        	],
        	[
        		'slug' => 'normal_user',
        		'name' => 'Normal User',
        		'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now()
        	],
        	[
        		'slug' => 'can_login_admin',
        		'name' => 'Can Login Admin',
        		'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now()
        	]
        ];

        DB::table('permissions')->insert($permissions);
    }
}

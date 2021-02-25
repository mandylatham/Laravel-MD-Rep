<?php

use Illuminate\Database\Seeder;
use App\AdminUser;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		AdminUser::insert([
    		'id' 				=> 1,
            'name' 				=> "Admin",
            'email' 			=> "admin@funclub.com",
            'phone' 			=> "1234567890",
            'tel_code' 			=> "+91",
            'password' 			=> bcrypt("secret"),
            'user_type'         => 1,
            'code' 		        => hash('md5',"ADMIN_0001"),
            'created_by' 		=> 1,
            'updated_by' 		=> 1,
            'active' 			=> 1,
    	]);
    }
}

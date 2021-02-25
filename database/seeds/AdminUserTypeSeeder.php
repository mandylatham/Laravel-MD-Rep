<?php

use Illuminate\Database\Seeder;
use App\AdminUserType;

class AdminUserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userTypes = config("sample.userType");
        $i = 1;
    	foreach ($userTypes as $value) {
    		AdminUserType::insert([
	    		'id' 			=> $i,
	            'name' 			=> $value['name'],
	            'code' 			=> $value['code'],
	            'admin_type' 	=> $value['admin_type'],
	            'created_by' 	=> 1,
	            'updated_by' 	=> 1,
	            'active' 		=> 1,
        	]);
        	$i++;
    	}
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = config("sample.permission");
        $i = 1;
    	foreach ($permission as $value) {
    		Permission::insert([
	    		'id' 			=> $i,
	            'name' 			=> $value['name'],
	            'code' 			=> $value['code'],
	            'type' 			=> 2,
	            'created_by' 	=> 1,
	            'updated_by' 	=> 1,
	            'active' 		=> 1,
        	]);
        	$i++;
    	}
    }
}

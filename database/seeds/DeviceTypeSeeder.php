<?php

use Illuminate\Database\Seeder;
use App\DeviceType;

class DeviceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deviceType = config("sample.deviceType");
        $i = 1;
    	foreach ($deviceType as $value) {
    		DeviceType::insert([
	    		'id' 			=> $i,
	            'name' 			=> $value['name'],
	            'code' 			=> $value['code'],
	            'mobile_type'   => $value['mobile_device'],
	            'created_by' 	=> 1,
	            'updated_by' 	=> 1,
	            'active' 		=> 1,
        	]);
        	$i++;
    	}
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = config("sample.gender");
        $i = 1;
    	foreach ($genders as $value) {
    		Gender::insert([
	    		'id' 			=> $i,
	            'name' 			=> $value['name'],
	            'code' 			=> $value['code'],
	            'created_by' 	=> 1,
	            'updated_by' 	=> 1,
	            'active' 		=> 1,
        	]);
        	$i++;
    	}
    }
}

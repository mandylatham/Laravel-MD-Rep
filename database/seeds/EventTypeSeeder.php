<?php

use Illuminate\Database\Seeder;
use App\EventType;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventTypes = config("sample.eventType");
        $i = 1;
    	foreach ($eventTypes as $value) {
    		EventType::insert([
	    		'id' 			=> $i,
	            'name' 			=> $value['name'],
	            'created_by' 	=> 1,
	            'updated_by' 	=> 1,
	            'active' 		=> 1,
        	]);
        	$i++;
    	}
    }
}

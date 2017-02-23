<?php

use Illuminate\Database\Seeder;

class OlinSeatTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('oli_seat')->insert(array(
				'seat'=>'Olivos Medellin',
				'phone'=>'+57 4 5134949',
				'description'=>NULL				
				)
		);
		
		
	}
}

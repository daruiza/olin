<?php

use Illuminate\Database\Seeder;

class OlinUserXSeatTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('oli_user_x_seat')->insert(array(
			'user_id'=>1,
			'seat_id'=>1
			)
		);
		\DB::table('oli_user_x_seat')->insert(array(
			'user_id'=>2,
			'seat_id'=>1
			)
		);
	}
}

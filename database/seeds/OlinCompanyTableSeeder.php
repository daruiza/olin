<?php

use Illuminate\Database\Seeder;

class OlinCompanyTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('oli_company')->insert(array(
				'company'=>'Cooperativa Consumo',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'Almacenes EXITO',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'Cooperativa Cootrames',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'Cooperativa Confiar',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'INSCRA s.a.s Lebon',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'Cooperativa Cofinep',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'CFA',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'Cooperativa Belen',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
		\DB::table('oli_company')->insert(array(
				'company'=>'Corpotración Fomentamos',
				'description'=>NULL,
				'seat_id'=>1
				)
		);
	}
}

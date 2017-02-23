<?php

use Illuminate\Database\Seeder;

class UserProfileTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('seg_user_profile')->insert(array(
			'identificacion'=>1039420501,
			'names'=>'Super',
			'surnames'=>'Administrator',
			'birthdate'=>'1988/05/25',
			'sex'=>'Masculino',
			'adress'=>'Cr 65 #51A-55',
			'avatar'=>'default.png',
			'description'=>'default',
			'template'=>'default',
			'movil_number'=>3122500351,
			'fix_number'=>5713329,
			'location'=>57,
			'user_id'=>1
			)
			);
		\DB::table('seg_user_profile')->insert(array(
			'identificacion'=>1039420502,
			'names'=>'Admin',
			'surnames'=>'Administrator',
			'birthdate'=>'1988/05/25',
			'sex'=>'Masculino',
			'adress'=>'Cr 65 #51A-55',
			'avatar'=>'default.png',
			'description'=>'default',
			'template'=>'default',
			'movil_number'=>3122500351,
			'fix_number'=>5713329,
			'location'=>57,
			'user_id'=>2
			)
		);
		\DB::table('seg_user_profile')->insert(array(
			'identificacion'=>1039420503,
			'names'=>'Consultor',
			'surnames'=>'Olin',
			'birthdate'=>'1988/05/25',
			'sex'=>'Masculino',
			'adress'=>'Cr 65 #51A-55',
			'avatar'=>'default.png',
			'description'=>'default',
			'template'=>'default',
			'movil_number'=>3122500351,
			'fix_number'=>5713329,
			'location'=>57,
			'user_id'=>3
			)
		);
		\DB::table('seg_user_profile')->insert(array(
			'identificacion'=>1039420504,
			'names'=>'Agente',
			'surnames'=>'Callcenter',
			'birthdate'=>'1988/05/25',
			'sex'=>'Masculino',
			'adress'=>'Cr 65 #51A-55',
			'avatar'=>'default.png',
			'description'=>'default',
			'template'=>'default',
			'movil_number'=>3122500351,
			'fix_number'=>5713329,
			'location'=>57,
			'user_id'=>4
			)
		);
	}
}

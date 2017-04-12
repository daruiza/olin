<?php

use Illuminate\Database\Seeder;

class AppTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('seg_app')->insert(array(
				'app'=>'Seguridad',
				'description'=>'Contiene todos los modulos de seguridad',
				'preferences'=>'{"icono":"glyphicon glyphicon-lock","js":"seguridad"}',
				'active'=>1
				)
		);
		\DB::table('seg_app')->insert(array(
				'app'=>'Integrador',
				'description'=>'Contiene los modulos de OLIN',
				'preferences'=>'{"icono":"glyphicon glyphicon-lock","js":"olin"}',
				'active'=>1
			)
		);
		\DB::table('seg_app')->insert(array(
				'app'=>'Homenajes',
				'description'=>'Contiene los modulos de Homenajes',
				'preferences'=>'{"icono":"glyphicon glyphicon-lock","js":"homenajes"}',
				'active'=>1
			)
		);
		
	}
}

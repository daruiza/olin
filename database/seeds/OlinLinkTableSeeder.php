<?php

use Illuminate\Database\Seeder;

class OlinLinkTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('oli_link')->insert(array(
				'link'=>'Convenio de Homenajes',
				'description'=>NULL			
				)
		);
		\DB::table('oli_link')->insert(array(
				'link'=>'Prevision',
				'description'=>NULL				
				)
		);
		\DB::table('oli_link')->insert(array(
				'link'=>'Prenecesidad',
				'description'=>NULL				
				)
		);
		
	}
}

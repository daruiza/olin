<?php

use Illuminate\Database\Seeder;

class HomReciverTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('hom_reciver')->insert(array(
			'email'=>'daruiza@gmail.com',
			'name'=>'David Andres Ruiz',
			'topic'=>'servicio',			
			'description'=>'Este es un correo personal de prueba'				
			)
		);
		\DB::table('hom_reciver')->insert(array(
			'email'=>'davidr@thinkwg.co',
			'name'=>'David Andres Ruiz',
			'topic'=>'servicio',			
			'description'=>'Este es un correo personal de prueba'				
			)
		);
	}
}

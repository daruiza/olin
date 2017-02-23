<?php

use Illuminate\Database\Seeder;

class RolTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('seg_rol')->insert(array(
			'rol'=>'Super Administrador',
			'description'=>'Posee acceso a todas las aplicaciones y sus opciones'				
			)
		);
		\DB::table('seg_rol')->insert(array(
			'rol'=>'Administrador',
			'description'=>'Administra todas las opciones de una sede'
			)
		);
		\DB::table('seg_rol')->insert(array(
			'rol'=>'Consultor',
			'description'=>'Puede realizar consultas a la base de datos'
			)
		);
		\DB::table('seg_rol')->insert(array(
			'rol'=>'Agente',
			'description'=>'Agente de Callcenter que realiza el primer contacto del servicio'
			)
		);
	}
}
<?php

use Illuminate\Database\Seeder;

class HomStateTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('hom_state')->insert(array(
			'state'=>'Llamada procesada',
			'alert'=>'#fcf8e3',
			'order'=>'2',
			'description'=>'Sucede cuando no hubo comunicación telefonica entre Call center y Coordinador'				
			)
		);
		\DB::table('hom_state')->insert(array(
			'state'=>'Devolver llamada',						
			'alert'=>'#f2dede',	
			'order'=>'1',
			'description'=>'El coordinador ha recibido el mensaje'
			)
		);
		\DB::table('hom_state')->insert(array(
			'state'=>'Servicio iniciado',						
			'alert'=>'#d9edf7',	
			'order'=>'3',
			'description'=>'El coordinador ha devuelto la llamada al titular o al responsable'
			)
		);
		\DB::table('hom_state')->insert(array(
			'state'=>'Servicio prestado',						
			'alert'=>'#dff0d8',	
			'order'=>'4',
			'description'=>'El serviocio se realizo acorde con la labor'
			)
		);
		\DB::table('hom_state')->insert(array(
			'state'=>'Servicio negado',						
			'alert'=>'',	
			'order'=>'5',
			'description'=>'El serviocio no se realizo acorde con la labor'
			)
		);
	}
}

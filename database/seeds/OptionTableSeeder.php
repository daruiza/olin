<?php

use Illuminate\Database\Seeder;

class OptionTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('seg_option')->insert(array(
				'option'=>'Listar',
				'action'=>'enumerar',
				'preference'=>'{"lugar":"escritorio","vista":"none","icono":"ui-icon-plus"}',
				'active'=>1
				)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Listar',
				'action'=>'enumerar',
				'preference'=>'{"lugar":"papelera","vista":"none","icono":"ui-icon-plus"}',
				'active'=>1
				)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Ver',
				'action'=>'mirar',
				'preference'=>'{"lugar":"escritorio","vista":"listar","icono":"glyphicon glyphicon-list-alt"}',
				'active'=>1
				)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Agregar',
				'action'=>'crear',
				'preference'=>'{"lugar":"escritorio","vista":"listar","icono":"glyphicon glyphicon-plus-sign"}',
				'active'=>1
				)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Agregar',
				'action'=>'crear',
				'preference'=>'{"lugar":"escritorio","vista":"none","icono":"glyphicon glyphicon-plus-sign"}',
				'active'=>1
				)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Editar',
				'action'=>'actualizar',
				'preference'=>'{"lugar":"escritorio","vista":"listar","icono":"glyphicon glyphicon-cog"}',
				'active'=>1
			)
		);	
		\DB::table('seg_option')->insert(array(
				'option'=>'Reciclar',
				'action'=>'botar',
				'preference'=>'{"lugar":"escritorio","vista":"listar","icono":"glyphicon glyphicon-trash"}',
				'active'=>1
			)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Restaurar',
				'action'=>'recuperar',
				'preference'=>'{"lugar":"papelera","vista":"listar","icono":"glyphicon glyphicon-repeat"}',
				'active'=>1
			)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Borrar',
				'action'=>'eliminar',
				'preference'=>'{"lugar":"papelera","vista":"listar","icono":"glyphicon glyphicon-minus-sign"}',
				'active'=>1
			)
		);	
		\DB::table('seg_option')->insert(array(
				'option'=>'Borrar',
				'action'=>'eliminar',
				'preference'=>'{"lugar":"escritorio","vista":"listar","icono":"glyphicon glyphicon-minus-sign"}',
				'active'=>1
			)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'Borrar',
				'action'=>'eliminar',
				'preference'=>'{"lugar":"escritorio","vista":"none","icono":"glyphicon glyphicon-minus-sign"}',
				'active'=>1
			)
		);
		\DB::table('seg_option')->insert(array(
				'option'=>'ExportarPDF',
				'action'=>'exportarpdf',
				'preference'=>'{"lugar":"escritorio","vista":"listar","icono":"glyphicon glyphicon-download"}',
				'active'=>1
			)
		);
		
	}
}
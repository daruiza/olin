<?php

use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('seg_module')->insert(array(
			'module'=>'Aplicaciones',
			'preference'=>'{"js":"seg_aplicacion","categoria":"Componentes","controlador":"/aplicacion/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de las aplicaciones de la pieza de software',
			'active'=>1,
			'app_id'=>1
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Modulos',
			'preference'=>'{"js":"seg_modulo","categoria":"Componentes","controlador":"/modulo/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los modulos de la aplicación',
			'active'=>1,
			'app_id'=>1
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Opciones',
			'preference'=>'{"js":"seg_opcion","categoria":"Componentes","controlador":"/opcion/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de las opciones de los modulos de la aplicación',
			'active'=>1,
			'app_id'=>1
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Permisos',
			'preference'=>'{"js":"seg_permiso","categoria":"Acceso","controlador":"/permiso/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los permisos de los usuarios en la aplicación',
			'active'=>1,
			'app_id'=>1
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Roles',
			'preference'=>'{"js":"seg_rol","categoria":"Componentes","controlador":"/rol/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los roles que pueden tomar los usuarios de la aplicación',
			'active'=>1,
			'app_id'=>1
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Usuarios',
			'preference'=>'{"js":"seg_usuario","categoria":"Agentes","controlador":"/usuario/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los usuarios de la aplicación',
			'active'=>1,
			'app_id'=>1
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Afiliados',
			'preference'=>'{"js":"oli_afiliado","categoria":"Prevision","controlador":"/afiliados/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los afiliados a Vivir los Olivos',
			'active'=>1,
			'app_id'=>2
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Logs',
			'preference'=>'{"js":"oli_log","categoria":"Administracion","controlador":"/logs/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de la actividad de la aplicación',
			'active'=>1,
			'app_id'=>2
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Empresas',
			'preference'=>'{"js":"oli_empresa","categoria":"Administracion","controlador":"/empresas/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de las empresas afiliadas',
			'active'=>1,
			'app_id'=>2
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Vinculos',
			'preference'=>'{"js":"oli_vinculo","categoria":"Administracion","controlador":"/vinculos/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los tipos de vinculo de Vivir los Olivos',
			'active'=>1,
			'app_id'=>2
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Sedes',
			'preference'=>'{"js":"oli_sede","categoria":"Administracion","controlador":"/sedes/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de las sedes de Vivir los Olivos',
			'active'=>1,
			'app_id'=>2
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Estado',
			'preference'=>'{"js":"hom_estado","categoria":"Componentes","controlador":"/estado/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo compone el modulo de Solicitud',
			'active'=>1,
			'app_id'=>3
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Solicitud',
			'preference'=>'{"js":"hom_solicitud","categoria":"Administracion","controlador":"/solicitud/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de las solicitudes realizadas desde el call center de OLIVOS',
			'active'=>1,
			'app_id'=>3
			)
		);
		\DB::table('seg_module')->insert(array(
			'module'=>'Directorio',
			'preference'=>'{"js":"hom_receptor","categoria":"Componentes","controlador":"/directorio/","uiicono":"ui-jqueri","icono":""}',
			'description'=>'Este modulo contine toda la información de los correos electronicos receptores del servicio radicado en el call center de OLIVOS',
			'active'=>1,
			'app_id'=>3
			)
		);
		\DB::table('seg_module')->insert(array(
				'module'=>'Consola',
				'preference'=>'{"js":"hom_consola","categoria":"Acceso","controlador":"/consola/","uiicono":"ui-jqueri","icono":""}',
				'description'=>'En este modulo se pueden realizar consutas directamente a las base de datos',
				'active'=>1,
				'app_id'=>1
			)
		);
		
	}
}

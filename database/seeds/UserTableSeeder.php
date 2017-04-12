<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Illuminate\Database\Seeder {
	
	public function run(){
		\DB::table('seg_user')->insert(array(
			'name'=>'superadmin',
			'email'=>'super@yopmail.com',
			'password'=>\Hash::make('0000'),
			'active'=>1,
			'login'=>0,
			'ip'=>'0',
			'rol_id'=>1
			)
		);
		\DB::table('seg_user')->insert(array(
			'name'=>'admin',
			'email'=>'admin@yopmail.com',
			'password'=>\Hash::make('0000'),
			'active'=>1,
			'login'=>0,
			'ip'=>'0',
			'rol_id'=>2
			)
		);
		\DB::table('seg_user')->insert(array(
			'name'=>'consultor',
			'email'=>'consultor@yopmail.com',
			'password'=>\Hash::make('0000'),
			'active'=>1,
			'login'=>0,
			'ip'=>'0',
			'rol_id'=>3
			)
		);
		\DB::table('seg_user')->insert(array(
			'name'=>'agente',
			'email'=>'agente@yopmail.com',
			'password'=>\Hash::make('0000'),
			'active'=>1,
			'login'=>0,
			'ip'=>'0',
			'rol_id'=>4
			)
		);
	}
}
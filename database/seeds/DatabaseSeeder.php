<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('RolTableSeeder');
		$this->call('UserTableSeeder');
		$this->call('UserProfileTableSeeder');
		$this->call('AppTableSeeder');
		$this->call('AppXUserTableSeeder');
		$this->call('ModuleTableSeeder');
		$this->call('OptionTableSeeder');
		$this->call('PermitTableSeeder');
		$this->call('OlinSeatTableSeeder');
		$this->call('OlinCompanyTableSeeder');
		$this->call('OlinLinkTableSeeder');
		$this->call('OlinLinkXCompanyTableSeeder');
		$this->call('OlinUserXSeatTableSeeder');	
		$this->call('HomStateTableSeeder');	
		$this->call('HomReciverTableSeeder');	
		 
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('seg_rol', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('rol', 60)->nullable();
			$table->string('description', 60)->nullable();	
			$table->timestamps();
		});
    	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_rol');
    }
}

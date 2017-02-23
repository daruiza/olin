<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('seg_option', function(Blueprint $table){
			$table->increments('id');
			$table->string('option', 60)->nullable();
			$table->string('action', 60)->nullable();
			$table->string('preference', 256)->nullable();
			$table->boolean('active')->default(true);
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
        Schema::drop('seg_option');
    }
}

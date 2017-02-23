<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOliVinculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('oli_link', function(Blueprint $table)
    	{
    		$table->increments('id');    		
    		$table->string('link');
    		$table->string('description', 256)->nullable();
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
    	Schema::drop('oli_link');
    }
}

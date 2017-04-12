<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hom_request', function(Blueprint $table)
    	{
    		$table->increments('id');
    		$table->string('name', 60);    		
    		$table->string('fhone', 60)->nullable();
    		$table->string('cellfhone', 60)->nullable();
    		$table->string('identification_headline', 60);
    		$table->string('name_headline', 60);
    		$table->string('seat')->nullable();
    		$table->string('identification_homage', 60);
    		$table->string('name_homage', 60);
    		$table->string('location_homage', 60);
    		$table->string('orden_service', 60);
    		$table->dateTime('date_service');
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
    	Schema::drop('hom_request');
    }
}


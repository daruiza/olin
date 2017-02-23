<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOliCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('oli_company', function(Blueprint $table)
    	{
    		$table->increments('id');
    		$table->string('company'); 
    		$table->string('description', 256)->nullable();
    		$table->integer('seat_id')->unsigned();
    		$table->foreign('seat_id')->references('id')->on('oli_seat')->onDelete('cascade');
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
    	Schema::drop('oli_company');
    }
}

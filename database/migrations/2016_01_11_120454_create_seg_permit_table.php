<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegPermitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('seg_permit',function(Blueprint $table){
    		$table->integer('rol_id')->unsigned();
    		$table->integer('module_id')->unsigned();
    		$table->integer('option_id')->unsigned();    	
    		$table->foreign('rol_id')->references('id')->on('seg_rol')->onDelete('cascade');
    		$table->foreign('module_id')->references('id')->on('seg_module')->onDelete('cascade');
    		$table->foreign('option_id')->references('id')->on('seg_option')->onDelete('cascade');
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
        Schema::drop('seg_permit');
    }
}

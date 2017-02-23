<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('seg_module',function(Blueprint $table){
    		$table->increments('id');
    		$table->string('module', 60)->nullable();
    		$table->string('preference', 256)->nullable();
    		$table->string('description', 256)->nullable();
    		$table->boolean('active')->default(true);
    		$table->integer('app_id')->unsigned();
    		$table->foreign('app_id')->references('id')->on('seg_app')->onDelete('cascade');
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
        Schema::drop('seg_module');
    }
}

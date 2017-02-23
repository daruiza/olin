<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomRequestXStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('hom_request_x_state', function(Blueprint $table)
    	{
    		$table->integer('state_id')->unsigned();
    		$table->integer('request_id')->unsigned();
    		$table->timestamp('date');
    		$table->string('description_state', 60)->nullable();
    		$table->foreign('state_id')->references('id')->on('hom_state')->onDelete('cascade');
    		$table->foreign('request_id')->references('id')->on('hom_request')->onDelete('cascade');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::drop('hom_request_x_state');
    }
}

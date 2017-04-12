<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOliLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oli_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('action', 60);
			$table->string('description', 256);
			$table->date('date');			
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('seg_user')->onDelete('cascade');
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
    	Schema::drop('oli_log');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOliUserXSeatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('oli_user_x_seat', function(Blueprint $table){
    		$table->integer('user_id')->unsigned();
    		$table->integer('seat_id')->unsigned();    		   		
    		$table->foreign('user_id')->references('id')->on('seg_user')->onDelete('cascade');    		
    		$table->foreign('seat_id')->references('id')->on('oli_seat')->onDelete('cascade');
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
    	Schema::drop('oli_user_x_seat');
    }
}

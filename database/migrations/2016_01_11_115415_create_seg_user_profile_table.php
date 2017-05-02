<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('seg_user_profile', function(Blueprint $table)
    	{
    		$table->increments('id');
    		$table->integer('identificacion');
    		$table->string('names', 60)->nullable();
    		$table->string('surnames', 60)->nullable();
    		$table->date('birthdate')->nullable();
    		$table->string('sex')->nullable();
    		$table->string('adress')->nullable();
    		$table->string('avatar', 60)->nullable();
    		$table->string('description', 240)->nullable();
    		$table->string('template', 60)->nullable();
    		$table->bigInteger('movil_number')->default(0);
    		$table->bigInteger('fix_number')->default(0);
    		$table->integer('location')->default(0);
    		$table->timestamps();
    		$table->integer('user_id')->unsigned();    		
    		$table->foreign('user_id')->references('id')->on('seg_user')->onDelete('cascade');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_user_profile');
    }
}

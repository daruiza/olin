<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomReciverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('hom_reciver', function(Blueprint $table)
    	{
    		$table->increments('id');
    		$table->string('email', 120)->unique();
    		$table->string('name', 60);
    		$table->string('topic', 60);    		
    		$table->string('description', 60)->nullable();
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
    	Schema::drop('hom_reciver');
    }
}

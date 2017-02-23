<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('seg_app', function(Blueprint $table)
    	{
    		$table->increments('id');
    		$table->string('app', 60);
    		$table->string('description', 60)->nullable();
    		$table->string('preferences', 60)->nullable();
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
        Schema::drop('seg_app');
    }
}

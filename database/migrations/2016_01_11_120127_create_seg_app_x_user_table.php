<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegAppXUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('seg_app_x_user', function(Blueprint $table)
		{
			$table->integer('app_id')->unsigned();
			$table->integer('user_id')->unsigned();			
			$table->boolean('active')->default(true);
			$table->foreign('app_id')->references('id')->on('seg_app')->onDelete('cascade');
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
        Schema::drop('seg_app_x_user');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOliLinkXCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oli_link_x_company', function(Blueprint $table)
    	{
    		$table->integer('link_id')->unsigned();
    		$table->integer('company_id')->unsigned();    		
    		$table->foreign('link_id')->references('id')->on('oli_link')->onDelete('cascade');
    		$table->foreign('company_id')->references('id')->on('oli_company')->onDelete('cascade');
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
    	Schema::drop('oli_link_x_company');
    }
}

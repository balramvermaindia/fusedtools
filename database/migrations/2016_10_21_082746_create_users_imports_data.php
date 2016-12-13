<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersImportsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_imports_data', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('users_import_id');
			$table->string('csv_field');
			$table->string('infusionsoft_field');
			$table->enum('infusionsoft_field_type', ['default', 'custum'])->default('default'); 	
			$table->integer('infusionsoft_field_id')->default(0);
			$table->string('value');
			$table->integer('row_number');
			$table->integer('field_order');
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
        Schema::drop('users_imports_data');
    }
}

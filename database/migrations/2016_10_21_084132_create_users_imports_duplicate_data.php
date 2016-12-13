<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersImportsDuplicateData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_imports_duplicate_data', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('users_import_id');
			$table->integer('row_number');
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
        Schema::drop('users_imports_duplicate_data');
    }
}

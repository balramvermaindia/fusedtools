<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('users_imports', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->date('start_date');
			$table->string('csv_file');
			$table->integer('is_account_id');
			$table->enum('filter_display', ['display', 'update'])->default('update'); 	
			$table->enum('filter_contact', ['both', 'create', 'update'])->default('both'); 	
			$table->enum('filter_company', ['both', 'create', 'update'])->default('both'); 	
			$table->integer('filter_duplicate')->default(5);
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
        Schema::drop('users_imports');
    }
}

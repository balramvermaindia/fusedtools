<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersIsAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('users_is_accounts', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id');
            $table->string('access_token');
            $table->string('referesh_token');
            $table->dateTime('expire_date');
            $table->string('account');
            $table->integer('active')->default('1');
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
        Schema::drop('users_is_accounts');
    }
}

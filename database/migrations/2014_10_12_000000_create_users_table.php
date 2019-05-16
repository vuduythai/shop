<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Backend\Core\System;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('permission')->nullable();
        });

        Schema::create('backend_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email', 128)->unique();
            $table->string('password');
            $table->tinyInteger('status')->default(System::STATUS_UNACTIVE)
                ->comment('0:unactive,1:active');
            $table->string('avatar', 255)->nullable();
            $table->text('permission')->nullable();
            $table->integer('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email', 128)->unique();
            $table->string('password');
            $table->tinyInteger('status')->default(System::STATUS_UNACTIVE)
                ->comment('0:unactive,1:active');
            $table->text('avatar')->nullable();
            $table->string('active_code', 128)->nullable();
            $table->timestamp('active_code_expire')->nullable();
            $table->rememberToken();//for logout
            $table->timestamps();
        });

        Schema::create('users_extends', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->integer('user_id')->unsigned();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('phone', 32)->nullable();
            $table->string('email');
            $table->string('address', 255)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('backend_users');
        Schema::dropIfExists('users');
        Schema::dropIfExists('users_extends');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

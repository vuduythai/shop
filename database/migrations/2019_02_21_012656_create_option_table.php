<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->tinyInteger('type')->comment('1:select,2:radio,3:checkbox,4:multi_select');
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
        });

        Schema::create('option_value', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->decimal('price', 15, 2)->nullable();
            $table->tinyInteger('type')->comment('1:fixed, 2:percentage');
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
            $table->integer('option_id')->unsigned();//unsigned => fk
            $table->foreign('option_id')
                ->references('id')->on('option')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('product_to_option', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();//unsigned
            $table->integer('option_id')->unsigned();//unsigned
            $table->tinyInteger('option_type');
            $table->integer('value_id')->unsigned();//unsigned
            $table->decimal('value_price', 15, 2)->nullable();
            $table->tinyInteger('value_type')->comment('1:fixed, 2:percentage');
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('order_option', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();//unsigned
            $table->integer('product_id')->unsigned();//unsigned
            $table->integer('option_id')->unsigned();//unsigned
            $table->string('option_name', 128);
            $table->tinyInteger('option_type');
            $table->integer('value_id')->unsigned();//unsigned
            $table->string('value_name', 128);
            $table->decimal('value_price', 15, 2)->nullable();
            $table->tinyInteger('value_type')->comment('1:fixed, 2:percentage');
            $table->foreign('order_id')
                ->references('id')->on('order');//add foreign key
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
        Schema::dropIfExists('option');
        Schema::dropIfExists('option_value');
        Schema::dropIfExists('option_to_value');
        Schema::dropIfExists('product_to_option');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

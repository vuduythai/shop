<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_set', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->text('attribute_json')->nullable();
        });

        Schema::create('attribute_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
        });

        Schema::create('attribute', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->tinyInteger('type')->comment('1:text, 2:color');
            $table->tinyInteger('is_filter')->comment('0:no, 1:yes');
            $table->tinyInteger('is_display')->comment('0:no, 1:yes');
            $table->integer('attribute_group_id')->unsigned();
            $table->foreign('attribute_group_id')
                ->references('id')->on('attribute_group');//add foreign key
        });

        Schema::create('attribute_property', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('value', 255)->nullable();
            $table->tinyInteger('type')->comment('1:text, 2:color');
            $table->integer('attribute_id')->unsigned();//unsigned => fk
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
            $table->foreign('attribute_id')
                ->references('id')->on('attribute')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('product_to_property', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();//unsigned => fk
            $table->integer('attribute_id')->unsigned();
            $table->integer('property_id')->unsigned()->default(0);
            $table->text('value')->nullable();
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
            $table->index('property_id');//add index
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onDelete('cascade');//add foreign key
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
        Schema::dropIfExists('attribute_group');
        Schema::dropIfExists('attribute_set');
        Schema::dropIfExists('attribute');
        Schema::dropIfExists('attribute_property');
        Schema::dropIfExists('attribute_to_property');
        Schema::dropIfExists('product_to_attribute_property');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

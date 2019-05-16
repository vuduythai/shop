<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 128);
            $table->string('slug', 128)->unique();
            $table->text('value')->nullable();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('slug', 128);
            $table->integer('entity_id')->unsigned();
            $comment = '1: product, 2: category, 3: page';
            $table->tinyInteger('type')->comment($comment);
            $table->index('slug');//add index
            $table->index('entity_id');//add index
        });

        //for frontend config theme
        Schema::create('theme', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 128);
            $table->string('slug', 128)->unique();
            $table->text('description')->nullable();
        });


        //for frontend config theme - gallery and image type
        Schema::create('theme_image', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('link', 512)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('alt', 255)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('theme_to_image', function (Blueprint $table) {
            $table->integer('theme_id')->unsigned();// for fk
            $table->integer('theme_image_id')->unsigned();// for fk
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
            $table->foreign('theme_id')
                ->references('id')->on('theme')->onDelete('cascade');//add foreign key
            $table->foreign('theme_image_id')
                ->references('id')->on('theme_image')->onDelete('cascade');//add foreign key
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
        Schema::dropIfExists('config');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('theme');
        Schema::dropIfExists('theme_image');
        Schema::dropIfExists('theme_to_image');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

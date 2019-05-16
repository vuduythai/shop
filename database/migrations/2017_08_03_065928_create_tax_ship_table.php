<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTaxShipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_zone', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name');
            $table->text('description')->nullable();
        });

        Schema::create('currency', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name');
            $table->string('code', 10);
            $table->string('symbol', 10);
            $table->tinyInteger('symbol_position')->comment('0: before, 1: after');
            $table->decimal('value', 15, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('ship_rule', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 255);
            $table->decimal('above_price', 15, 2)->nullable();
            $table->integer('geo_zone_id')->unsigned()->nullable();
            $table->tinyInteger('weight_type')->nullable()->comment('1:fixed, 2:rate');
            $table->string('weight_based', 512)->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->tinyInteger('type')
                ->comment('1: price, 2: geo, 3: weight based, 4: per item, 5: geo weight based');
            $table->tinyInteger('status');
        });

        Schema::create('tax_class', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
        });

        Schema::create('tax_rate', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 255);
            $table->tinyInteger('type')->comment('0: percentage, 1: fixed amount');
            $table->unsignedInteger('geo_zone_id');// for fk
            $table->decimal('rate', 15, 2);
            $table->foreign('geo_zone_id')->references('id')->on('geo_zone');//add foreign key
        });

        Schema::create('tax_rule', function (Blueprint $table) {
            $table->unsignedInteger('tax_class_id');
            $table->unsignedInteger('tax_rate_id');// for fk
            $table->foreign('tax_class_id')->references('id')->on('tax_class')
                ->onDelete('cascade');//add foreign key
            $table->foreign('tax_rate_id')->references('id')->on('tax_rate')
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
        Schema::dropIfExists('geo_zone');
        Schema::dropIfExists('currency');
        Schema::dropIfExists('ship_rule');
        Schema::dropIfExists('tax_class');
        Schema::dropIfExists('tax_rate');
        Schema::dropIfExists('tax_rule');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

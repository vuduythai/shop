<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\System;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('color', 128);
        });

        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('billing_first_name', 128);
            $table->string('billing_last_name', 128);
            $table->string('billing_address', 128);
            $table->string('billing_email', 128);
            $table->string('billing_phone', 32);
            $table->string('shipping_first_name', 128);
            $table->string('shipping_last_name', 128);
            $table->string('shipping_address', 128);
            $table->string('shipping_email', 128);
            $table->string('shipping_phone', 32);
            $table->integer('shipping_rule_id');
            $table->string('payment_method', 128);
            $table->text('comment')->nullable();
            $table->decimal('shipping_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->integer('order_status_id')->unsigned();
            $table->tinyInteger('payment_status')->comment('0: not paid; 1: paid');
            $table->timestamps();
            $table->foreign('order_status_id')
                ->references('id')->on('order_status');//add foreign key
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();//for fk
            $table->integer('product_id')->unsigned();
            $table->integer('variant_id')->unsigned()->default(0)->comment('0 if product is simple type');
            $table->string('name', 512)->comment('Name of product at the time customer make order');
            $table->integer('qty')->unsigned();
            $table->decimal('price_after_tax', 15, 2)->comment('price of product at the time customer make order');
            $table->decimal('total', 15, 2);
            $table->decimal('weight', 15, 2);
            $table->tinyInteger('weight_id');
            $table->foreign('order_id')
                ->references('id')->on('order')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('order_status_change', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('order_status_id')->unsigned();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->foreign('order_id')
                ->references('id')->on('order');//add foreign key
        });
        Schema::create('payment', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 128);
            $table->string('code', 128)->unique();//if let strength is 255 -> unique -> so long -> error
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(System::ENABLE);
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
        Schema::dropIfExists('order_status');
        Schema::dropIfExists('order');
        Schema::dropIfExists('order_product');
        Schema::dropIfExists('order_status_change');
        Schema::dropIfExists('payment');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

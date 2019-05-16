<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('code', 255);
            $table->tinyInteger('type')->comment('1: percentage, 2: fixed amount');
            $table->tinyInteger('logged')->comment('need customer logged in ? 0: no, 1: yes');
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable()
                ->comment('total amount that has to be reached before the coupon is valid');
            $table->timestamp('start_date')->nullable()->comment('coupon is valid from this date');
            $table->timestamp('end_date')->nullable()->comment('coupon is valid to this date');
            $table->integer('num_uses')->default(0)->comment('number times this coupon can be used');
            $table->integer('num_per_customer')->nullable()
                ->comment('number times single customers can use for this coupon');
            $table->tinyInteger('status');
            $table->tinyInteger('is_for_all')->default(\Modules\Backend\Models\Coupon::IS_FOR_ALL)
                ->comment('0:not for all, 1: for all');
            $table->timestamps();
        });

        //to know how many times coupon used, and how many customers use this coupon
        Schema::create('coupon_history', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->integer('coupon_id')->unsigned();//fk
            $table->integer('order_id')->unsigned();//fk
            $table->integer('customer_id')->unsigned();//fk
            $table->decimal('total', 15, 2)->nullable();
            $table->timestamps();
            $table->foreign('coupon_id')
                ->references('id')->on('coupon')
                ->onDelete('cascade');//add foreign key
            $table->foreign('order_id')
                ->references('id')->on('order')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('coupon_to_product', function (Blueprint $table) {
            $table->integer('coupon_id')->unsigned();//unsigned
            $table->integer('product_id')->unsigned();//unsigned
            $table->foreign('coupon_id')
                ->references('id')->on('coupon')
                ->onDelete('cascade');//add foreign key
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('coupon_to_category', function (Blueprint $table) {
            $table->integer('coupon_id')->unsigned();//unsigned
            $table->integer('category_id')->unsigned();//unsigned
            $table->foreign('coupon_id')
                ->references('id')->on('coupon')
                ->onDelete('cascade');//add foreign key
            $table->foreign('category_id')
                ->references('id')->on('categories')
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
        Schema::dropIfExists('coupon');
        Schema::dropIfExists('coupon_history');
        Schema::dropIfExists('coupon_to_product');
        Schema::dropIfExists('coupon_to_category');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

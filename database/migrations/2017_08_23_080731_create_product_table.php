<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Core\System;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 128);
            $table->string('slug', 128)->unique();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('price_promotion', 15, 2)->nullable();
            $table->timestamp('price_promo_from')->nullable();
            $table->timestamp('price_promo_to')->nullable();
            $table->string('sku', 128)->nullable();
            $table->tinyInteger('is_in_stock');
            $table->integer('qty')->nullable();
            $table->integer('qty_order')->nullable()->default(0);
            $table->text('image')->nullable();
            $table->text('gallery')->nullable();
            $table->tinyInteger('status');
            $table->integer('sort_order')->unsigned()->nullable();
            $table->integer('category_default')->unsigned()->nullable();
            $table->tinyInteger('product_type');
            $table->tinyInteger('is_has_option');
            $table->tinyInteger('is_variant_change_image');
            $table->integer('tax_class_id');
            $table->text('related_product')->nullable();
            $table->decimal('weight', 15, 2)->nullable();
            $table->integer('weight_id')->unsigned()->nullable();
            $table->string('product_label')->nullable();
            $table->tinyInteger('brand_id')->nullable();
            $table->integer('review_count')->nullable()->default(0);
            $table->tinyInteger('review_point')->nullable()->default(0)->comment('review point average');
            $table->tinyInteger('is_featured_product');
            $table->tinyInteger('is_new');
            $table->tinyInteger('is_bestseller');
            $table->tinyInteger('is_on_sale');
            $table->text('tag')->nullable();
            $table->text('short_intro')->nullable();
            $table->text('full_intro')->nullable();
            $table->text('seo_title')->nullable();
            $table->text('seo_keyword')->nullable();
            $table->text('seo_description')->nullable();
            $table->decimal('length', 15, 2)->nullable();
            $table->decimal('width', 15, 2)->nullable();
            $table->decimal('height', 15, 2)->nullable();
            $table->integer('length_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('product_variant', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned()->comment('id of product config parent');//unsigned => fk
            $table->string('property_string', 128)->nullable()->comment('property of product config child');
            $table->integer('qty_variant')->nullable();
            $table->integer('qty_order')->nullable()->default(0);
            $table->decimal('price_variant', 15, 2)->nullable();
            $table->text('variant_image')->nullable();
            $table->text('variant_gallery')->nullable();
            $table->index('product_id');//add index
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->string('name', 128);
            $table->string('slug', 128)->unique();
            $table->integer('parent_id')->unsigned()->nullable()->default(null);
            $table->integer('lft')->unsigned()->nullable()->default(null);
            $table->integer('rgt')->unsigned()->nullable()->default(null);
            $table->integer('depth')->unsigned()->nullable()->default(null);
            $table->tinyInteger('status');
            $table->text('seo_title')->nullable();
            $table->text('seo_keyword')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_homepage')->nullable()->default(0);
            $table->integer('num_display')->unsigned()->nullable()->default(0);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');//primary key, unsigned(10), index
            $table->integer('product_id')->unsigned();
            $table->integer('customer_id')->unsigned()->nullable()->default(0);
            $table->string('author');
            $table->text('content');
            $table->tinyInteger('rate')->nullable()->default(1);
            $table->tinyInteger('status')->nullable()->default(System::STATUS_ACTIVE);
            $table->timestamps();
            $table->foreign('product_id')
                ->references('id')->on('product')
                ->onDelete('cascade');//add foreign key
        });

        Schema::create('length', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('unit', 10);
            $table->decimal('value', 15, 2);
        });

        Schema::create('weight', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('unit', 10);
            $table->decimal('value', 15, 2);
        });

        Schema::create('brand', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('image');
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
        });

        Schema::create('label', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('text_display')->nullable();
            $table->string('image');
            $table->text('css_inline_text')->nullable();
            $table->text('css_inline_image')->nullable();
            $table->tinyInteger('type')->comment('1:image, 2:text on image');
        });

        Schema::create('product_to_category', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();//unsigned
            $table->integer('category_id')->unsigned();//unsigned
            $table->index('category_id');//add index
            $table->foreign('product_id')
                ->references('id')->on('product')
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
        Schema::dropIfExists('product');
        Schema::dropIfExists('product_extend');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('length');
        Schema::dropIfExists('weight');
        Schema::dropIfExists('brand');
        Schema::dropIfExists('label');
        Schema::dropIfExists('product_configurable');
        Schema::dropIfExists('product_to_category');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

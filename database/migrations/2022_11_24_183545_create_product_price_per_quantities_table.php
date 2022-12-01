<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricePerQuantitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_per_quantities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ec_products_id');
            $table->double('sale_price');
            $table->integer('quantity');
            $table->double('sale_rate');
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
        Schema::dropIfExists('product_price_per_quantities');
    }
}

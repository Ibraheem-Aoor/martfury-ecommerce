<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefundColumnsIntoEcProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_products', function (Blueprint $table) {
            $table->string('attr_weight');
            $table->string('attr_height');
            $table->string('attr_width');
            $table->string('attr_length');
            $table->string('product_country');
            $table->string('packaging_language');
            $table->string('product_meterial');
            $table->string('product_color');
            $table->string('peice_count');
            $table->boolean('is_guaranteed');
            $table->text('guarantee')->nullable();
            $table->string('delivery_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_products', function (Blueprint $table) {
            $table->dropColumn(['attr_weight' , 'attr_height' , 'attr_width' , 'attr_length' ,
            'product_country' , 'packaging_language' , 'product_meterial' , 'peice_count'  , 'is_guaranteed' ,
            'guarantee' , 'max_delivery_from' , 'max_delivery_to']);
        });
    }
}

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
            $table->boolean('is_refunded')->nullable();
            $table->text('refund_details')->nullable();
            $table->string('attr_weight');
            $table->string('attr_height');
            $table->string('attr_width');
            $table->string('attr_length');
            $table->string('product_country');
            $table->string('packaging_language');
            $table->string('product_meterial');
            $table->string('peice_count');
            $table->string('package_content');
            $table->boolean('is_guaranteed');
            $table->text('guarantee')->nullable();
            $table->bigInteger('max_delivery_from');
            $table->bigInteger('max_delivery_to');
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
            $table->dropColumn(['is_refunded' , 'refund_details' , 'attr_weight' , 'attr_height' , 'attr_width' , 'attr_length' ,
            'product_country' , 'packaging_language' , 'product_meterial' , 'peice_count' , 'package_content' , 'is_guaranteed' ,
            'guarantee' , 'max_delivery_from' , 'max_delivery_to']);
        });
    }
}

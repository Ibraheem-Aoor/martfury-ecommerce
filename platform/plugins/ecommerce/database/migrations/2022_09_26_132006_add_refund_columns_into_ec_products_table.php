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
            $table->string('attr_weight')->nullable();
            $table->string('attr_height')->nullable();
            $table->string('attr_width')->nullable();
            $table->string('attr_length')->nullable();
            $table->string('product_country')->nullable();
            $table->string('packaging_language')->nullable();
            $table->string('product_meterial')->nullable();
            $table->string('product_color')->nullable();
            $table->string('peice_count')->nullable();
            $table->boolean('is_guaranteed')->nullable();
            $table->text('guarantee')->nullable();
            $table->string('delivery_time')->nullable();
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
            'product_country' , 'packaging_language' , 'product_meterial' , 'peice_count'  , 'is_guaranteed' , 'delivery_time',
            'guarantee' ,]);
        });
    }
}

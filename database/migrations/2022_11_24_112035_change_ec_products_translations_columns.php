<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEcProductsTranslationsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_products_translations' , function(Blueprint $table){
            $table->longText('name')->nullable()->change();
            $table->longText('description')->nullable()->change();
            $table->longText('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_products_translations' , function(Blueprint $table){
            $table->dropColumn(['name' , 'description' , 'content']);
        });
    }
}

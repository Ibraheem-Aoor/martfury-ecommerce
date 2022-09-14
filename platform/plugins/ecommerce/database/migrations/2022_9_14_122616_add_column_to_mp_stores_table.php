<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Botble\Ecommerce\Enums\CustomerStatusEnum;

class AddColumnToMpStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('mp_stores', function (Blueprint $table) {
                $table->string('registration_country');
                $table->string('commerce_number');
                $table->string('tax_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mp_stores', function (Blueprint $table) {
            $table->dropColumn(['registration_country' , 'commerce_number' , 'tax_number']);
    });
    }
}

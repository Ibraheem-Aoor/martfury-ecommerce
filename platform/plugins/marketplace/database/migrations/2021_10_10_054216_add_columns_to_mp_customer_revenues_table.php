<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMpCustomerRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mp_customer_revenues', function (Blueprint $table) {
            $table->integer('user_id')->default(0)->unsigned();
            $table->string('type', 60)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mp_customer_revenues', function (Blueprint $table) {
            $table->dropColumn('user_id', 'type');
        });
    }
}

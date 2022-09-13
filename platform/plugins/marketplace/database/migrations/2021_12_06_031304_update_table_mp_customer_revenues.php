<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableMpCustomerRevenues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mp_customer_revenues', function (Blueprint $table) {
            $table->decimal('sub_amount', 15)->default(0)->nullable()->change();
            $table->decimal('amount', 15)->default(0)->nullable()->change();
            $table->decimal('current_balance', 15)->default(0)->nullable()->change();
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
            $table->decimal('sub_amount', 15)->default(0)->unsigned()->nullable()->change();
            $table->decimal('amount', 15)->default(0)->unsigned()->nullable()->change();
            $table->decimal('current_balance', 15)->default(0)->unsigned()->nullable()->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionIdColumnToMpCustomerWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mp_customer_withdrawals', function (Blueprint $table) {
            $table->string('transaction_id', 60)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mp_customer_withdrawals', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
    }
}

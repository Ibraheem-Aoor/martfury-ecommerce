<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnShippingCompanyNameToEcOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->string('shipping_company_name')->nullable();
            $table->string('shipping_tracking_id')->nullable();
            $table->string('shipping_tracking_link')->nullable();
            $table->date('estimate_arrival_date')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_orders', function (Blueprint $table) {
            $table->dropColumn('is_featured');
            $table->dropColumn(['shipping_company_name' , 'shipping_tracking_id' ,
                                'shipping_tracking_link' , 'estimate_arrival_date' , 'note']);
        });
    }
}

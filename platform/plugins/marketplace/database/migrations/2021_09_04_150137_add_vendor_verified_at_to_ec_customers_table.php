<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorVerifiedAtToEcCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec_customers', function (Blueprint $table) {
            if (!Schema::hasColumn('ec_customers', 'vendor_verified_at')) {
                $table->dateTime('vendor_verified_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec_customers', function (Blueprint $table) {
            if (Schema::hasColumn('ec_customers', 'vendor_verified_at')) {
                $table->dropColumn('vendor_verified_at');
            }
        });
    }
}

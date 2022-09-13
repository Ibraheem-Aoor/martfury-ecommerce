<?php

namespace Botble\Marketplace;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('mp_vendor_info');
        Schema::dropIfExists('mp_customer_revenues');
        Schema::dropIfExists('mp_customer_withdrawals');

        Schema::table('ec_products', function (Blueprint $table) {
            if (Schema::hasColumn('ec_products', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
        });

        Schema::table('ec_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ec_orders', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });

        Schema::table('ec_products', function (Blueprint $table) {
            if (Schema::hasColumn('ec_products', 'store_id')) {
                $table->dropColumn('store_id');
            }

            if (Schema::hasColumn('ec_products', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
        });

        Schema::table('ec_customers', function (Blueprint $table) {
            if (Schema::hasColumn('ec_customers', 'is_vendor')) {
                $table->dropColumn('is_vendor');
            }

            if (Schema::hasColumn('ec_customers', 'balance')) {
                $table->dropColumn('balance');
            }

            if (Schema::hasColumn('ec_customers', 'vendor_info_id')) {
                $table->dropColumn('vendor_info_id');
            }

            if (Schema::hasColumn('ec_customers', 'vendor_verified_at')) {
                $table->dropColumn('vendor_verified_at');
            }
        });

        Schema::dropIfExists('mp_stores');
    }
}

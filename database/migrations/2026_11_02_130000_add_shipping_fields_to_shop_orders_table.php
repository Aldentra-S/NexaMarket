<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 15, 2)->default(0.00)->after('subtotal');
            $table->string('courier', 50)->nullable()->after('shipping_cost');
            $table->string('tracking_number', 100)->nullable()->after('courier');
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_cost',
                'courier',
                'tracking_number',
                'shipped_at',
            ]);
        });
    }
};


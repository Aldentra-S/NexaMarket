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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_recipient_name', 100)->nullable()->after('notes');
            $table->string('shipping_phone', 30)->nullable()->after('shipping_recipient_name');
            $table->string('shipping_province', 100)->nullable()->after('shipping_phone');
            $table->string('shipping_city', 100)->nullable()->after('shipping_province');
            $table->string('shipping_district', 100)->nullable()->after('shipping_city');
            $table->string('shipping_postal_code', 20)->nullable()->after('shipping_district');
            $table->text('shipping_address')->nullable()->after('shipping_postal_code');
            $table->text('shipping_delivery_notes')->nullable()->after('shipping_address');
            $table->decimal('shipping_cost_total', 15, 2)->default(0.00)->after('shipping_delivery_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_recipient_name',
                'shipping_phone',
                'shipping_province',
                'shipping_city',
                'shipping_district',
                'shipping_postal_code',
                'shipping_address',
                'shipping_delivery_notes',
                'shipping_cost_total',
            ]);
        });
    }
};


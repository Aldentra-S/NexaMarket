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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('snap_token', 100)->nullable()->after('payment_method');
            $table->string('snap_redirect_url')->nullable()->after('snap_token');
            $table->string('gateway_transaction_id', 100)->nullable()->index()->after('snap_redirect_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['gateway_transaction_id']);
            $table->dropColumn(['snap_token', 'snap_redirect_url', 'gateway_transaction_id']);
        });
    }
};

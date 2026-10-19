<?php

use App\Enums\ShopStatus;
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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->unique();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->text('description');
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default(ShopStatus::PENDING->value)->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_id')->constrained()->cascadeOnDelete();
            $table->string('exchange_order_id', 64)->nullable();
            $table->string('type', 20)->default('market');
            $table->string('status', 20)->default('pending');
            $table->decimal('filled_quantity', 24, 8)->default(0);
            $table->decimal('remaining_quantity', 24, 8)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

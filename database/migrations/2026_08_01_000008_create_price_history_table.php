<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20);
            $table->string('exchange', 30)->default('binance');
            $table->decimal('open', 24, 8);
            $table->decimal('high', 24, 8);
            $table->decimal('low', 24, 8);
            $table->decimal('close', 24, 8);
            $table->decimal('volume', 24, 8)->default(0);
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->index(['symbol', 'exchange', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};

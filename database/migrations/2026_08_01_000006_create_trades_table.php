<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('strategy_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('exchange', 30)->default('binance');
            $table->string('symbol', 20);
            $table->enum('side', ['buy', 'sell']);
            $table->enum('type', ['market', 'limit', 'stop_loss', 'take_profit']);
            $table->decimal('quantity', 24, 8);
            $table->decimal('price', 24, 8)->nullable();
            $table->decimal('total', 24, 8)->nullable();
            $table->decimal('fee', 24, 8)->default(0);
            $table->enum('status', ['pending', 'open', 'filled', 'partial', 'cancelled', 'failed'])->default('pending');
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};

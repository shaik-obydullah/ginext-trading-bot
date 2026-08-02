<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('symbol', 20);
            $table->decimal('quantity', 24, 8)->default(0);
            $table->decimal('avg_buy_price', 24, 8)->default(0);
            $table->decimal('current_value', 24, 8)->default(0)->nullable();
            $table->timestamps();

            $table->unique(['portfolio_id', 'symbol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_assets');
    }
};

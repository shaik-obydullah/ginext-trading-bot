<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('start_value', 24, 8)->default(0);
            $table->decimal('end_value', 24, 8)->default(0);
            $table->decimal('pnl', 24, 8)->default(0);
            $table->decimal('pnl_percentage', 12, 4)->default(0);
            $table->timestamps();

            $table->unique(['portfolio_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_performance');
    }
};

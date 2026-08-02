<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 20);
            $table->string('indicator_type', 20);
            $table->decimal('value', 24, 8);
            $table->integer('period')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index(['symbol', 'indicator_type', 'calculated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};

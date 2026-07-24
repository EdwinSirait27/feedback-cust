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
        Schema::create('daily_prize_quotas', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedSmallInteger('voucher_100k_used')->default(0);
            $table->unsignedSmallInteger('voucher_50k_used')->default(0);
            $table->unsignedSmallInteger('voucher_25k_used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        Schema::dropIfExists('daily_prize_quotas');
    }
};

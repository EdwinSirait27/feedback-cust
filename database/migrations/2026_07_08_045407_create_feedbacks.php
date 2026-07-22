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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('session_token')->unique(); // dipakai untuk resume progresif (cookie/localStorage)
            $table->string('language', 5)->default('id');

            // Page 2 — wajib
            // $table->string('receipt_number')->unique();
            $table->string('receipt_number')->nullable()->unique(); // tambah nullable()

            $table->unsignedBigInteger('total_purchase')->nullable();
            $table->string('country')->nullable();
            $table->unsignedTinyInteger('overall_rating')->nullable();

            // Page 3 — Penilaian Layanan
            $table->unsignedTinyInteger('staff_friendliness')->nullable();
            $table->unsignedTinyInteger('staff_helpfulness')->nullable();
            $table->unsignedTinyInteger('checkout_process')->nullable();
            $table->unsignedTinyInteger('service_speed')->nullable();

            // Page 4 — Penilaian Toko
            $table->unsignedTinyInteger('store_cleanliness')->nullable();
            $table->unsignedTinyInteger('store_ambience')->nullable();
            $table->unsignedTinyInteger('product_display')->nullable();
            $table->unsignedTinyInteger('product_variety')->nullable();

            // Page 5 — Penilaian Produk & Harga
            $table->unsignedTinyInteger('product_quality')->nullable();
            $table->unsignedTinyInteger('product_design')->nullable();
            $table->unsignedTinyInteger('price_appropriateness')->nullable();
            $table->unsignedTinyInteger('value_for_money')->nullable();

            // Page 6 — Komentar & Saran
            $table->text('like_most')->nullable();
            $table->text('improvement_suggestion')->nullable();
            $table->unsignedTinyInteger('recommend_likelihood')->nullable(); // NPS 0-10

            // Page 7 — Informasi Tambahan (opsional)
            $table->boolean('may_contact')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();

            // Progress & scoring
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->unsignedSmallInteger('total_points')->default(0);
            $table->timestamp('completed_at')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};

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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Siapa yang memberikan ulasan?
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            // Siapa yang menerima ulasan?
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();

            $table->integer('rating'); // Bintang 1 sampai 5
            $table->text('comment'); // Isi komentar ulasan

            $table->timestamps();

            // Mencegah user ngespam review ke orang yang sama berulang kali
            $table->unique(['reviewer_id', 'reviewee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

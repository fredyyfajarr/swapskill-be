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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            // Menyambungkan ke tabel users (siapa yang menyimpan)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Menyambungkan ke tabel posts (postingan apa yang disimpan)
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Mencegah 1 user menyimpan postingan yang sama berkali-kali
            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};

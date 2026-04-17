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
            // reviewer_id = Orang yang memberi bintang
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            // reviewee_id = Orang yang dinilai profilnya
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating'); // Menyimpan angka 1 sampai 5
            $table->text('comment');
            $table->timestamps();
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

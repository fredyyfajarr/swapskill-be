<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();

            // Siapa yang memberi ulasan
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            // Siapa yang diulas (teman barternya)
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('rating'); // Bintang 1 sampai 5
            $table->text('comment'); // Komentar ulasan
            $table->timestamps();

            // Mencegah 1 orang memberi ulasan berkali-kali di postingan yang sama
            $table->unique(['post_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

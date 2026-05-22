<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barter_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->text('message')->nullable();
            $table->boolean('requester_confirmed_complete')->default(false);
            $table->boolean('owner_confirmed_complete')->default(false);
            $table->timestamps();
        });

        // Add barter_request_id to reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('barter_request_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['barter_request_id']);
            $table->dropColumn('barter_request_id');
        });
        Schema::dropIfExists('barter_requests');
    }
};

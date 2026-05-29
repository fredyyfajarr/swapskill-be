<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'receiver_id', 'created_at'], 'messages_sender_receiver_created_at_idx');
            $table->index(['receiver_id', 'sender_id', 'created_at'], 'messages_receiver_sender_created_at_idx');
            $table->index(['receiver_id', 'read_at'], 'messages_receiver_read_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_sender_receiver_created_at_idx');
            $table->dropIndex('messages_receiver_sender_created_at_idx');
            $table->dropIndex('messages_receiver_read_at_idx');
        });
    }
};

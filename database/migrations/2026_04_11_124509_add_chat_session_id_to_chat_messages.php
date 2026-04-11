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
        Schema::table('chat_messages', function (Blueprint $table) {
            // Add chat_session_id column after user_id
            $table->foreignId('chat_session_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');

            // Add indexes for better performance
            $table->index(['chat_session_id', 'created_at']);
            $table->index(['user_id', 'chat_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['chat_session_id']);

            // Drop the column
            $table->dropColumn('chat_session_id');

            // Drop indexes
            $table->dropIndex(['chat_session_id', 'created_at']);
            $table->dropIndex(['user_id', 'chat_session_id']);
        });
    }
};

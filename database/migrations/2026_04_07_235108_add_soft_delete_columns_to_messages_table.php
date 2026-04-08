<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add soft delete columns if they don't exist
            if (!Schema::hasColumn('messages', 'is_deleted_by_sender')) {
                $table->boolean('is_deleted_by_sender')->default(false)->after('read_at');
            }

            if (!Schema::hasColumn('messages', 'is_deleted_by_receiver')) {
                $table->boolean('is_deleted_by_receiver')->default(false)->after('is_deleted_by_sender');
            }

            // Add indexes only if they don't already exist
            try {
                // Check if index exists before creating
                $indexExists = false;
                $indexes = DB::select('SHOW INDEX FROM messages WHERE Key_name = ?', ['messages_sender_id_receiver_id_created_at_index']);
                if (empty($indexes)) {
                    $table->index(['sender_id', 'receiver_id', 'created_at'], 'messages_sender_id_receiver_id_created_at_index');
                }
            } catch (\Exception $e) {
                // Index might already exist with different name, continue
            }

            try {
                $indexExists = false;
                $indexes = DB::select('SHOW INDEX FROM messages WHERE Key_name = ?', ['messages_receiver_id_read_index']);
                if (empty($indexes)) {
                    $table->index(['receiver_id', 'read'], 'messages_receiver_id_read_index');
                }
            } catch (\Exception $e) {
                // Continue if index exists
            }

            try {
                $indexExists = false;
                $indexes = DB::select('SHOW INDEX FROM messages WHERE Key_name = ?', ['messages_is_deleted_by_sender_is_deleted_by_receiver_index']);
                if (empty($indexes)) {
                    $table->index(['is_deleted_by_sender', 'is_deleted_by_receiver'], 'messages_is_deleted_by_sender_is_deleted_by_receiver_index');
                }
            } catch (\Exception $e) {
                // Continue if index exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('messages', 'is_deleted_by_sender')) {
                $table->dropColumn('is_deleted_by_sender');
            }

            if (Schema::hasColumn('messages', 'is_deleted_by_receiver')) {
                $table->dropColumn('is_deleted_by_receiver');
            }

            // Drop indexes if they exist
            try {
                $table->dropIndex('messages_sender_id_receiver_id_created_at_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('messages_receiver_id_read_index');
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex('messages_is_deleted_by_sender_is_deleted_by_receiver_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });
    }
};

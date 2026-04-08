<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('messages', 'is_deleted_by_sender')) {
                $table->boolean('is_deleted_by_sender')->default(false)->after('read_at');
            }

            if (!Schema::hasColumn('messages', 'is_deleted_by_receiver')) {
                $table->boolean('is_deleted_by_receiver')->default(false)->after('is_deleted_by_sender');
            }

            // Add indexes for better performance if they don't exist
            $indexes = collect(\DB::select('SHOW INDEX FROM messages'))->pluck('Key_name')->toArray();

            if (!in_array('messages_sender_receiver_index', $indexes)) {
                $table->index(['sender_id', 'receiver_id'], 'messages_sender_receiver_index');
            }

            if (!in_array('messages_receiver_read_index', $indexes)) {
                $table->index(['receiver_id', 'read'], 'messages_receiver_read_index');
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'is_deleted_by_sender')) {
                $table->dropColumn('is_deleted_by_sender');
            }

            if (Schema::hasColumn('messages', 'is_deleted_by_receiver')) {
                $table->dropColumn('is_deleted_by_receiver');
            }

            $table->dropIndexIfExists('messages_sender_receiver_index');
            $table->dropIndexIfExists('messages_receiver_read_index');
        });
    }
};

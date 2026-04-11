<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Add chat_session_id column if it doesn't exist
            if (!Schema::hasColumn('chat_messages', 'chat_session_id')) {
                $table->foreignId('chat_session_id')->after('user_id')->constrained()->onDelete('cascade');
            }

            // Add tokens_used column if it doesn't exist
            if (!Schema::hasColumn('chat_messages', 'tokens_used')) {
                $table->integer('tokens_used')->nullable()->after('message');
            }

            // Add response_time_ms column if it doesn't exist
            if (!Schema::hasColumn('chat_messages', 'response_time_ms')) {
                $table->integer('response_time_ms')->nullable()->after('tokens_used');
            }

            // Add indexes only if they don't exist
            $this->addIndexIfNotExists('chat_messages', ['chat_session_id', 'created_at'], 'chat_messages_chat_session_id_created_at_index');
            $this->addIndexIfNotExists('chat_messages', ['user_id', 'chat_session_id'], 'chat_messages_user_id_chat_session_id_index');
        });
    }

    /**
     * Add index if it doesn't exist
     */
    protected function addIndexIfNotExists($table, $columns, $indexName)
    {
        try {
            // Check if index exists
            $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);

            if (empty($result)) {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            // If checking fails, try to add it anyway
            try {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
        }
    }

    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('chat_messages', 'chat_session_id')) {
                $table->dropForeign(['chat_session_id']);
                $table->dropColumn('chat_session_id');
            }

            if (Schema::hasColumn('chat_messages', 'tokens_used')) {
                $table->dropColumn('tokens_used');
            }

            if (Schema::hasColumn('chat_messages', 'response_time_ms')) {
                $table->dropColumn('response_time_ms');
            }

            // Drop indexes
            $table->dropIndex('chat_messages_chat_session_id_created_at_index');
            $table->dropIndex('chat_messages_user_id_chat_session_id_index');
        });
    }
};

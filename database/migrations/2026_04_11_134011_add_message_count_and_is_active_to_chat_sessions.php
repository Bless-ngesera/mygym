<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_sessions', 'message_count')) {
                $table->integer('message_count')->default(0)->after('last_message_at');
            }
            if (!Schema::hasColumn('chat_sessions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('message_count');
            }
        });
    }

    public function down()
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('chat_sessions', 'message_count')) {
                $table->dropColumn('message_count');
            }
            if (Schema::hasColumn('chat_sessions', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};

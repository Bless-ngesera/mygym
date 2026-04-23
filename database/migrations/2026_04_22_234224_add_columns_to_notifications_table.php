<?php
// database/migrations/2026_01_15_000003_add_columns_to_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add role column (who should receive this notification)
            $table->enum('role', ['admin', 'instructor', 'member'])->default('member')->after('user_id');

            // Add priority column (critical, high, medium, low)
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium')->after('message');

            // Add action URL for clickable notifications
            $table->string('action_url')->nullable()->after('data');

            // Add expiration date for auto-deletion
            $table->timestamp('expires_at')->nullable()->after('read_at');

            // Add delivery tracking timestamp
            $table->timestamp('delivered_at')->nullable()->after('expires_at');

            // Add indexes for better performance
            $table->index(['user_id', 'read_at']);
            $table->index(['priority', 'expires_at']);
            $table->index(['role', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['role', 'priority', 'action_url', 'expires_at', 'delivered_at']);
            $table->dropIndex(['user_id', 'read_at']);
            $table->dropIndex(['priority', 'expires_at']);
            $table->dropIndex(['role', 'created_at']);
            $table->dropIndex(['type', 'created_at']);
        });
    }
};

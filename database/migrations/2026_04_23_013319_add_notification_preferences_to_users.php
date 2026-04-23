<?php
// database/migrations/2026_04_23_000002_add_notification_preferences_to_users.php

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
        Schema::table('users', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('users', 'push_token')) {
                $table->string('push_token')->nullable()->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'device_type')) {
                $table->enum('device_type', ['ios', 'android', 'web'])->nullable()->after('push_token');
            }

            if (!Schema::hasColumn('users', 'last_notification_read_at')) {
                $table->timestamp('last_notification_read_at')->nullable()->after('updated_at');
            }

            // Add index for faster queries
            if (!Schema::hasIndex('users', 'users_push_token_index')) {
                $table->index('push_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'push_token')) {
                $table->dropColumn('push_token');
            }

            if (Schema::hasColumn('users', 'device_type')) {
                $table->dropColumn('device_type');
            }

            if (Schema::hasColumn('users', 'last_notification_read_at')) {
                $table->dropColumn('last_notification_read_at');
            }
        });
    }
};

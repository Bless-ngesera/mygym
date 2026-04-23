<?php
// database/migrations/2026_04_23_010900_add_channels_column_to_notifications.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Only add channels column if it doesn't exist
            if (!Schema::hasColumn('notifications', 'channels')) {
                $table->json('channels')->nullable()->after('data');
            }
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'channels')) {
                $table->dropColumn('channels');
            }
        });
    }
};

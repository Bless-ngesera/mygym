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
        Schema::table('users', function (Blueprint $table) {
            // Basic user preferences
            $table->boolean('notification_email')->default(true)->after('remember_token');
            $table->enum('email_frequency', ['instant', 'daily', 'weekly', 'never'])
                  ->default('daily')
                  ->after('notification_email');
            $table->string('language', 10)->default('en')->after('email_frequency');
            $table->string('timezone', 50)->default('UTC')->after('language');
            $table->enum('theme', ['light', 'dark', 'system'])->default('system')->after('timezone');
            $table->timestamp('last_login_at')->nullable()->after('theme');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notification_email',
                'email_frequency',
                'language',
                'timezone',
                'theme',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};

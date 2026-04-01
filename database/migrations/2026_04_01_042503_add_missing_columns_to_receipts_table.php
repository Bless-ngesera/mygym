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
        Schema::table('receipts', function (Blueprint $table) {
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('receipts', 'status')) {
                $table->string('status')->default('completed')->after('amount');
            }

            // Add paid_at column if it doesn't exist
            if (!Schema::hasColumn('receipts', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('receipts', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('receipts', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};

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
        Schema::table('scheduled_classes', function (Blueprint $table) {
            // First, drop the existing unique constraint on date_time
            $table->dropUnique('scheduled_classes_date_time_unique');

            // Add composite unique constraint for instructor + date_time
            $table->unique(['instructor_id', 'date_time'], 'unique_instructor_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_classes', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('unique_instructor_datetime');

            // Restore the original unique constraint on date_time
            $table->unique('date_time', 'scheduled_classes_date_time_unique');
        });
    }
};

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
        Schema::table('workouts', function (Blueprint $table) {
            if (!Schema::hasColumn('workouts', 'calories_burn')) {
                $table->integer('calories_burn')->nullable()->after('duration');
            }

            if (!Schema::hasColumn('workouts', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('duration');
            }

            if (!Schema::hasColumn('workouts', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('date');
            }

            if (!Schema::hasColumn('workouts', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn(['calories_burn', 'duration_minutes', 'started_at', 'completed_at']);
        });
    }
};

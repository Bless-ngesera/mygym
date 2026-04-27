<?php

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
        Schema::table('bookings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('bookings', 'booking_reference')) {
                $table->string('booking_reference', 50)->nullable()->unique()->after('scheduled_class_id');
            }

            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('booked_at');
            }

            if (!Schema::hasColumn('bookings', 'checked_in')) {
                $table->boolean('checked_in')->default(false)->after('status');
            }

            if (!Schema::hasColumn('bookings', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('checked_in');
            }

            // Add indexes only if they don't exist
            $this->addIndexIfNotExists($table, ['user_id', 'status'], 'idx_bookings_user_status');
            $this->addIndexIfNotExists($table, ['scheduled_class_id', 'status'], 'idx_bookings_class_status');
        });

        // Generate booking references for existing records
        $existingBookings = DB::table('bookings')->whereNull('booking_reference')->get();
        foreach ($existingBookings as $booking) {
            DB::table('bookings')
                ->where('id', $booking->id)
                ->update([
                    'booking_reference' => 'BK-' . strtoupper(uniqid()) . '-' . rand(1000, 9999)
                ]);
        }

        // Make booking_reference NOT NULL after populating (if it was nullable)
        if (Schema::hasColumn('bookings', 'booking_reference')) {
            $hasNull = DB::table('bookings')->whereNull('booking_reference')->exists();
            if (!$hasNull) {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->string('booking_reference', 50)->nullable(false)->change();
                });
            }
        }
    }

    /**
     * Add index if it doesn't exist
     */
    private function addIndexIfNotExists($table, $columns, $indexName)
    {
        $conn = Schema::getConnection();
        $tableName = $conn->getTablePrefix() . 'bookings';

        // Check if index exists
        $indexes = $conn->select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$indexName]);

        if (empty($indexes)) {
            $table->index($columns, $indexName);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndexIfExists('idx_bookings_user_status');
            $table->dropIndexIfExists('idx_bookings_class_status');

            // Drop columns
            $table->dropColumnIfExists('booking_reference');
            $table->dropColumnIfExists('cancelled_at');
            $table->dropColumnIfExists('checked_in');
            $table->dropColumnIfExists('checked_in_at');
        });
    }
};

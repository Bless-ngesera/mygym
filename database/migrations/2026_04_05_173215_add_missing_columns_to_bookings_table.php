<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('bookings', 'booked_at')) {
                $table->timestamp('booked_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'status')) {
                $table->string('status')->default('confirmed');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booked_at', 'status']);
        });
    }
};

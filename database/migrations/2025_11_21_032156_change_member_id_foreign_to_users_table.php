<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Safely drop old foreign key if it exists
            try {
                $table->dropForeign(['member_id']);
            } catch (\Exception $e) {
                // ignore if it doesn't exist
            }

            // Add new foreign key to users
            $table->foreign('member_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['member_id']);

            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('status', ['checked_in', 'checked_out'])->default('checked_out');
            $table->timestamps();

            $table->index(['user_id', 'check_in']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance');
    }
};

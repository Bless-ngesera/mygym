<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['weight', 'workouts', 'attendance', 'strength', 'nutrition']);
            $table->decimal('target_value', 10, 2);
            $table->decimal('current_value', 10, 2)->default(0);
            $table->string('unit')->nullable(); // kg, sessions, times, etc.
            $table->date('deadline');
            $table->enum('status', ['active', 'achieved', 'failed'])->default('active');
            $table->timestamp('achieved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'deadline']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('goals');
    }
};

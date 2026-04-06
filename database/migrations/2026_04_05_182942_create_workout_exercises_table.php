<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('sets')->default(3);
            $table->integer('reps')->default(10);
            $table->integer('rest_seconds')->default(60);
            $table->integer('weight_kg')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->unique(['workout_id', 'exercise_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workout_exercises');
    }
};

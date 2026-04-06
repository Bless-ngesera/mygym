<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nutrition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('calories')->default(0);
            $table->integer('protein_grams')->default(0);
            $table->integer('carbs_grams')->default(0);
            $table->integer('fat_grams')->default(0);
            $table->integer('fiber_grams')->default(0);
            $table->integer('sugar_grams')->default(0);
            $table->integer('water_ml')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('nutrition_logs');
    }
};

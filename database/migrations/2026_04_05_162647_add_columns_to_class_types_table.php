<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->integer('capacity')->default(20)->after('minutes');
            $table->string('image')->nullable()->after('capacity');
            $table->string('color')->default('#8b5cf6')->after('image');
            $table->string('icon')->default('🏋️')->after('color');
            $table->boolean('is_active')->default(true)->after('icon');
            $table->string('difficulty_level')->default('beginner')->after('is_active');
            $table->decimal('price', 10, 2)->nullable()->after('difficulty_level');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn([
                'capacity', 'image', 'color', 'icon',
                'is_active', 'difficulty_level', 'price'
            ]);
        });
    }
};

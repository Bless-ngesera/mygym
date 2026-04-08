<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('class_types', function (Blueprint $table) {
            if (!Schema::hasColumn('class_types', 'instructor_commission_percentage')) {
                $table->decimal('instructor_commission_percentage', 5, 2)->default(70.00)->after('price');
            }

            if (!Schema::hasColumn('class_types', 'requires_equipment')) {
                $table->boolean('requires_equipment')->default(false)->after('instructor_commission_percentage');
            }

            if (!Schema::hasColumn('class_types', 'equipment_list')) {
                $table->json('equipment_list')->nullable()->after('requires_equipment');
            }

            if (!Schema::hasColumn('class_types', 'benefits')) {
                $table->json('benefits')->nullable()->after('equipment_list');
            }

            if (!Schema::hasColumn('class_types', 'meetup_point')) {
                $table->string('meetup_point')->nullable()->after('benefits');
            }

            if (!Schema::hasColumn('class_types', 'what_to_bring')) {
                $table->text('what_to_bring')->nullable()->after('meetup_point');
            }

            // Add indexes
            $table->index(['is_active', 'difficulty_level']);
            $table->index(['price']);
        });
    }

    public function down()
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn([
                'instructor_commission_percentage',
                'requires_equipment',
                'equipment_list',
                'benefits',
                'meetup_point',
                'what_to_bring'
            ]);
        });
    }
};

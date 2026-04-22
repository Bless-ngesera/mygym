<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (!Schema::hasColumn('plans', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('plans', 'currency')) {
                $table->string('currency')->default('UGX')->after('price');
            }
            if (!Schema::hasColumn('plans', 'billing_period')) {
                $table->string('billing_period')->default('monthly')->after('currency');
            }
            if (!Schema::hasColumn('plans', 'duration_days')) {
                $table->integer('duration_days')->default(30)->after('billing_period');
            }
            if (!Schema::hasColumn('plans', 'features')) {
                $table->json('features')->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('plans', 'max_classes_per_week')) {
                $table->integer('max_classes_per_week')->nullable()->after('features');
            }
            if (!Schema::hasColumn('plans', 'has_personal_trainer')) {
                $table->boolean('has_personal_trainer')->default(false)->after('max_classes_per_week');
            }
            if (!Schema::hasColumn('plans', 'is_popular')) {
                $table->boolean('is_popular')->default(false)->after('has_personal_trainer');
            }
            if (!Schema::hasColumn('plans', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_popular');
            }
            if (!Schema::hasColumn('plans', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $columns = ['slug', 'description', 'currency', 'billing_period', 'duration_days',
                        'features', 'max_classes_per_week', 'has_personal_trainer', 'is_popular',
                        'sort_order', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

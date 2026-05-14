<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add constraints to prevent data corruption
     */
    public function up(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            try {
                $table->dropForeign(['fee_category_id']);
            } catch (\Exception $e) {
            }

            $table->foreign('fee_category_id')
                ->references('id')
                ->on('fee_categories')
                ->restrictOnDelete();
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->index(['student_id', 'fee_master_id', 'billing_period_start'], 'billings_student_fee_period_idx');
        });
    }

    public function down(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            try {
                $table->dropForeign(['fee_category_id']);
            } catch (\Exception $e) {
            }

            $table->foreign('fee_category_id')
                ->references('id')
                ->on('fee_categories')
                ->nullOnDelete();
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropIndex('billings_student_fee_period_idx');
        });
    }
};

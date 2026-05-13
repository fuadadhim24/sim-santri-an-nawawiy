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
        // Prevent deleting FeeCategory if it's used in FeeManager
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->foreign('fee_category_id')
                ->references('id')
                ->on('fee_categories')
                ->restrictOnDelete(); // Prevent deletion if referenced
        });

        // Prevent deleting Student if it has unpaid billings
        Schema::table('billings', function (Blueprint $table) {
            // Add unique index to prevent duplicate billings for same period
            $table->unique(['student_id', 'fee_master_id', 'billing_period_start'])
                ->where('status', '!=', 'CANCELLED');
        });
    }

    public function down(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->dropForeign(['fee_category_id']);
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'fee_master_id', 'billing_period_start']);
        });
    }
};

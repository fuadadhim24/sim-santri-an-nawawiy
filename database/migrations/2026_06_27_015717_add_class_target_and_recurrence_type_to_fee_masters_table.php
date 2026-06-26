<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->string('recurrence_type')->default('ONE_TIME')->change();
            $table->foreignId('class_level_target_id')->nullable()->after('residence_target')->constrained('class_levels')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->dropForeign(['class_level_target_id']);
            $table->dropColumn('class_level_target_id');
        });
    }
};

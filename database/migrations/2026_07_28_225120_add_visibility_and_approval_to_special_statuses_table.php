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
        Schema::table('special_statuses', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->after('is_system');
        });

        Schema::table('student_special_statuses', function (Blueprint $table) {
            $table->boolean('is_approved')->default(true)->after('status_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('special_statuses', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });

        Schema::table('student_special_statuses', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};

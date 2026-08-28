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
        if (Schema::hasColumn('students', 'special_status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('special_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('students', 'special_status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('special_status', 50)->nullable()->after('residence_status');
            });
        }
    }
};

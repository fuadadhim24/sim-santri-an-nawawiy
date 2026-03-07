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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'kk')) {
                $table->string('kk')->nullable()->after('address');
            }
            if (!Schema::hasColumn('students', 'foto')) {
                $table->string('foto')->nullable()->after('kk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('students', 'kk')) {
                $columnsToDrop[] = 'kk';
            }
            if (Schema::hasColumn('students', 'foto')) {
                $columnsToDrop[] = 'foto';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

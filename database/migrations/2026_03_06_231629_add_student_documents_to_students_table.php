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
            if (!Schema::hasColumn('students', 'akta')) {
                $table->string('akta')->nullable()->after('nisn');
            }
            if (!Schema::hasColumn('students', 'ijazah')) {
                $table->string('ijazah')->nullable()->after('akta');
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
            if (Schema::hasColumn('students', 'akta')) {
                $columnsToDrop[] = 'akta';
            }
            if (Schema::hasColumn('students', 'ijazah')) {
                $columnsToDrop[] = 'ijazah';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY COLUMN special_status ENUM('UMUM', 'ANAK_GURU', 'YATIM', 'PRESTASI', 'LINGKUNGAN') NOT NULL DEFAULT 'UMUM'");
            DB::statement("ALTER TABLE discounts MODIFY COLUMN target_status ENUM('ANAK_GURU', 'YATIM', 'PRESTASI', 'LINGKUNGAN') NOT NULL");
        }
    }

    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE students MODIFY COLUMN special_status ENUM('UMUM', 'ANAK_GURU', 'YATIM', 'PRESTASI') NOT NULL DEFAULT 'UMUM'");
            DB::statement("ALTER TABLE discounts MODIFY COLUMN target_status ENUM('ANAK_GURU', 'YATIM', 'PRESTASI') NOT NULL");
        }
    }
};

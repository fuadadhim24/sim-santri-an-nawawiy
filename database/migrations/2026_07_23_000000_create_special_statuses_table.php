<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('special_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Insert default system statuses
        DB::table('special_statuses')->insert([
            ['code' => 'UMUM', 'name' => 'Umum', 'description' => 'Golongan Umum (Tanpa Potongan/Diskon)', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ANAK_GURU', 'name' => 'Anak Guru', 'description' => 'Anak Guru/Karyawan', 'is_system' => false, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'YATIM', 'name' => 'Yatim', 'description' => 'Anak Yatim/Yatim Piatu', 'is_system' => false, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PRESTASI', 'name' => 'Siswa Berprestasi', 'description' => 'Siswa Berprestasi Akademik/Non-Akademik', 'is_system' => false, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'LINGKUNGAN', 'name' => 'Lingkungan', 'description' => 'Santri dari Lingkungan Sekitar', 'is_system' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $isSqlite = config('database.default') === 'sqlite' || DB::connection() instanceof \Illuminate\Database\SQLiteConnection;

        if ($isSqlite) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('special_status', 50)->default('UMUM')->change();
            });
            Schema::table('discounts', function (Blueprint $table) {
                $table->string('target_status', 50)->change();
            });
        } else {
            DB::statement("ALTER TABLE students MODIFY COLUMN special_status VARCHAR(50) NOT NULL DEFAULT 'UMUM'");
            DB::statement("ALTER TABLE discounts MODIFY COLUMN target_status VARCHAR(50) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isSqlite = config('database.default') === 'sqlite' || DB::connection() instanceof \Illuminate\Database\SQLiteConnection;

        if ($isSqlite) {
            // Can't revert to ENUM easily in SQLite, but changing back to string is okay
        } else {
            DB::statement("ALTER TABLE students MODIFY COLUMN special_status ENUM('UMUM', 'ANAK_GURU', 'YATIM', 'PRESTASI', 'LINGKUNGAN') NOT NULL DEFAULT 'UMUM'");
            DB::statement("ALTER TABLE discounts MODIFY COLUMN target_status ENUM('ANAK_GURU', 'YATIM', 'PRESTASI', 'LINGKUNGAN') NOT NULL");
        }

        Schema::dropIfExists('special_statuses');
    }
};

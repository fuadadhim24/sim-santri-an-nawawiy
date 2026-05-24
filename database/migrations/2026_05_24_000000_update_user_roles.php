<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN', 'ADMIN_TU', 'ADMINISTRASI', 'BENDAHARA', 'WALI_SANTRI') DEFAULT 'WALI_SANTRI'");
            DB::table('users')->where('role', 'ADMIN_TU')->update(['role' => 'ADMINISTRASI']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN', 'ADMINISTRASI', 'BENDAHARA', 'WALI_SANTRI') DEFAULT 'WALI_SANTRI'");
        } else {
            DB::table('users')->where('role', 'ADMIN_TU')->update(['role' => 'ADMINISTRASI']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN', 'ADMIN_TU', 'ADMINISTRASI', 'BENDAHARA', 'WALI_SANTRI') DEFAULT 'WALI_SANTRI'");
            DB::table('users')->where('role', 'ADMINISTRASI')->update(['role' => 'ADMIN_TU']);
            DB::table('users')->where('role', 'BENDAHARA')->update(['role' => 'ADMIN_TU']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPER_ADMIN', 'ADMIN_TU', 'WALI_SANTRI') DEFAULT 'WALI_SANTRI'");
        } else {
            DB::table('users')->where('role', 'ADMINISTRASI')->update(['role' => 'ADMIN_TU']);
            DB::table('users')->where('role', 'BENDAHARA')->update(['role' => 'ADMIN_TU']);
        }
    }
};

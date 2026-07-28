<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel pivot baru
        Schema::create('student_special_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status_code', 50);
            $table->timestamps();

            $table->unique(['student_id', 'status_code']);
            $table->foreign('status_code')->references('code')->on('special_statuses')->onDelete('cascade')->onUpdate('cascade');
        });

        // 2. Migrasi data existing: pindahkan special_status lama ke tabel pivot
        DB::statement("
            INSERT INTO student_special_statuses (student_id, status_code, created_at, updated_at)
            SELECT id, special_status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM students
            WHERE special_status != 'UMUM'
            AND special_status IS NOT NULL
            AND special_status != ''
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('student_special_statuses');
    }
};

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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('guardians')->onDelete('cascade');
            $table->string('nis', 20)->unique();
            $table->string('full_name');
            $table->enum('unit_code', ['01', '02', '03']); // 01=SMP, 02=SMA, 03=PPTQ
            $table->enum('residence_status', ['MONDOK', 'NON_MONDOK', 'NGAJI_ONLY']);
            $table->enum('special_status', ['UMUM', 'ANAK_GURU', 'YATIM'])->default('UMUM');
            $table->string('class_name')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

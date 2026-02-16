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
        Schema::create('fee_masters', function (Blueprint $table) {
            $table->id();
            $table->enum('unit_target', ['01', '02', '03'])->nullable();
            $table->enum('residence_target', ['MONDOK', 'NON_MONDOK'])->nullable();
            $table->enum('category', ['DAFTAR_ULANG', 'BULANAN', 'SEMESTERAN']);
            $table->string('item_name');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_masters');
    }
};

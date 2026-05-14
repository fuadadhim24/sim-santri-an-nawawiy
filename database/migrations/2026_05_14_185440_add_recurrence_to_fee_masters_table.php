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
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->enum('recurrence_type', ['ONE_TIME', 'MONTHLY', 'YEARLY'])->default('ONE_TIME')->after('amount');
            $table->integer('due_days')->default(14)->after('billing_day')->comment('Tenggat waktu pembayaran dalam hari setelah tagihan dibuat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->dropColumn(['recurrence_type', 'due_days']);
        });
    }
};

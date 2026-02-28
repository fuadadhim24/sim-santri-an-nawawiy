<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->foreignId('fee_master_id')->nullable()->after('student_id')->constrained('fee_masters')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropForeign(['fee_master_id']);
            $table->dropColumn('fee_master_id');
        });
    }
};

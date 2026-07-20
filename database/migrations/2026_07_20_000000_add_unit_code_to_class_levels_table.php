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
        Schema::table('class_levels', function (Blueprint $table) {
            $table->string('unit_code', 20)->nullable()->after('level_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_levels', function (Blueprint $table) {
            $table->dropColumn('unit_code');
        });
    }
};

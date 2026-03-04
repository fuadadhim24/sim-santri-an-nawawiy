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
        Schema::table('fee_categories', function (Blueprint $table) {
            $table->enum('unit_target', ['01', '02', '03'])->nullable()->after('can_generate_before_acceptance');
            $table->enum('domicile_target', ['MONDOK', 'NON_MONDOK', 'NGAJI_ONLY'])->nullable()->after('unit_target');
            $table->text('description')->nullable()->after('domicile_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_categories', function (Blueprint $table) {
            $table->dropColumn(['unit_target', 'domicile_target', 'description']);
        });
    }
};

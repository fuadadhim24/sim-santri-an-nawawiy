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
            $table->foreignId('fee_category_id')->nullable()->after('residence_target')->constrained('fee_categories')->nullOnDelete();
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->string('category')->after('residence_target');
            $table->dropForeign(['fee_category_id']);
            $table->dropColumn('fee_category_id');
        });
    }
};

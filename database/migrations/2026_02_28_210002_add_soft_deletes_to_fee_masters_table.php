<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('end_date');
            $table->foreignId('replaced_by')->nullable()->after('is_active')->constrained('fee_masters')->nullOnDelete();
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('fee_masters', function (Blueprint $table) {
            $table->dropForeign(['replaced_by']);
            $table->dropColumn([
                'is_active',
                'replaced_by',
                'deleted_at',
            ]);
        });
    }
};

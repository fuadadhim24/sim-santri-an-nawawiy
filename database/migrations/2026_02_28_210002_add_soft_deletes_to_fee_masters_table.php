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
        if (Schema::hasColumn('fee_masters', 'replaced_by')) {
            Schema::table('fee_masters', function (Blueprint $table) {
                $table->dropForeign(['replaced_by']);
            });
        }

        $columnsToDelete = [];
        if (Schema::hasColumn('fee_masters', 'is_active')) {
            $columnsToDelete[] = 'is_active';
        }
        if (Schema::hasColumn('fee_masters', 'replaced_by')) {
            $columnsToDelete[] = 'replaced_by';
        }
        if (Schema::hasColumn('fee_masters', 'deleted_at')) {
            $columnsToDelete[] = 'deleted_at';
        }

        if (!empty($columnsToDelete)) {
            Schema::table('fee_masters', function (Blueprint $table) use ($columnsToDelete) {
                $table->dropColumn($columnsToDelete);
            });
        }
    }
};

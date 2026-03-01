<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->foreignId('version_of')->nullable()->after('fee_master_id')->constrained('billings')->nullOnDelete();
            $table->integer('version')->default(1)->after('version_of');
            $table->boolean('visible_to_wali')->default(true)->after('status');
            $table->foreignId('archived_by')->nullable()->after('visible_to_wali')->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable()->after('archived_by');
            $table->text('archive_reason')->nullable()->after('archived_at');

            $table->index('version_of');
            $table->index('visible_to_wali');
            $table->index('archived_by');
        });

        if (!Schema::hasColumn('billings', 'deleted_at')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropForeign(['version_of']);
            $table->dropForeign(['archived_by']);
            $table->dropColumn([
                'version_of',
                'version',
                'visible_to_wali',
                'archived_by',
                'archived_at',
                'archive_reason',
                'deleted_at',
            ]);
        });
    }
};

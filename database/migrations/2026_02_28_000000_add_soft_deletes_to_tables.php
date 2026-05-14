<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'deleted_at')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('billings', 'deleted_at')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('payments', 'deleted_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('guardians', 'deleted_at')) {
            Schema::table('guardians', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};

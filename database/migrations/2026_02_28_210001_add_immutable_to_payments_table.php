<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('billing_id')->constrained('users')->nullOnDelete();
            $table->string('method')->default('cash')->after('amount');
            $table->string('duitku_reference')->nullable()->after('method');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('paid')->after('duitku_reference');
            $table->text('notes')->nullable()->after('status');
        });

        if (!Schema::hasColumn('payments', 'deleted_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Drop old payment_method column if exists
        if (Schema::hasColumn('payments', 'payment_method')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('billing_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn([
                'admin_id',
                'method',
                'duitku_reference',
                'status',
                'notes',
                'deleted_at',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_categories', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('code');
            $table->enum('activation_mode', ['single_active_per_key', 'multi_active', 'manual_only'])->default('multi_active')->after('is_locked');
            $table->boolean('can_generate_before_acceptance')->default(true)->after('activation_mode');
        });
    }

    public function down(): void
    {
        Schema::table('fee_categories', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'activation_mode', 'can_generate_before_acceptance']);
        });
    }
};

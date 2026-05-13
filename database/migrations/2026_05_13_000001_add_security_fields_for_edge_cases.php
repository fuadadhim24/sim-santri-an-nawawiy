<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add security fields for preventing edge cases
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Track when student joined
            $table->timestamp('joined_at')->nullable()->after('is_active');
            // Track when student left/graduated
            $table->timestamp('left_at')->nullable()->after('joined_at');
        });

        Schema::table('billings', function (Blueprint $table) {
            // Snapshot the price at billing creation time (immutable)
            $table->json('price_snapshot')->nullable()->after('final_amount');
            // Track when billing was generated
            $table->timestamp('billing_generated_at')->nullable()->after('price_snapshot');
            // Billing period start (for pro-rata calculations)
            $table->date('billing_period_start')->nullable()->after('billing_generated_at');
            // Billing period end
            $table->date('billing_period_end')->nullable()->after('billing_period_start');
            // Optional: Expiry date for billing (after which it's auto-cancelled)
            $table->timestamp('expires_at')->nullable()->after('billing_period_end');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Snapshot the amount at payment time (to verify against callback)
            $table->decimal('snapshot_billing_amount', 12, 2)->nullable()->after('duitku_reference');
            // Add index for faster lookup
            $table->index(['billing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'left_at']);
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn([
                'price_snapshot',
                'billing_generated_at',
                'billing_period_start',
                'billing_period_end',
                'expires_at',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('snapshot_billing_amount');
            $table->dropIndex(['billing_id', 'status']);
        });
    }
};

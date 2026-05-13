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
            $table->timestamp('joined_at')->nullable()->after('is_active');
            $table->timestamp('left_at')->nullable()->after('joined_at');
            
            $table->index('joined_at');
            $table->index('left_at');
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->json('price_snapshot')->nullable()->after('final_amount');
            $table->timestamp('billing_generated_at')->nullable()->after('price_snapshot');
            $table->date('billing_period_start')->nullable()->after('billing_generated_at');
            $table->date('billing_period_end')->nullable()->after('billing_period_start');
            $table->timestamp('expires_at')->nullable()->after('billing_period_end');
            
            $table->string('proration_type')->nullable()->after('expires_at');
            $table->decimal('proration_rate', 5, 4)->nullable()->after('proration_type');
            $table->text('proration_note')->nullable()->after('proration_rate');
            
            $table->index('expires_at');
            $table->index('payment_reference');
            $table->index('billing_period_start');
            $table->index('billing_period_end');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('snapshot_billing_amount', 12, 2)->nullable()->after('duitku_reference');
            $table->index(['billing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['joined_at']);
            $table->dropIndex(['left_at']);
            $table->dropColumn(['joined_at', 'left_at']);
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['payment_reference']);
            $table->dropIndex(['billing_period_start']);
            $table->dropIndex(['billing_period_end']);
            $table->dropColumn([
                'price_snapshot',
                'billing_generated_at',
                'billing_period_start',
                'billing_period_end',
                'expires_at',
                'proration_type',
                'proration_rate',
                'proration_note',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['billing_id', 'status']);
            $table->dropColumn('snapshot_billing_amount');
        });
    }
};

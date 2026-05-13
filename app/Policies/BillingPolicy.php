<?php

namespace App\Policies;

use App\Models\Billing;
use App\Models\User;

class BillingPolicy
{
    /**
     * SECURITY: Only guardian of the student can view the billing
     * Prevents IDOR: /print-invoice/123 -> /print-invoice/124
     */
    public function view(User $user, Billing $billing): bool
    {
        // Super admin can view all
        if ($user->role === 'SUPER_ADMIN') {
            return true;
        }

        // Admin TU can view all
        if ($user->role === 'ADMIN_TU') {
            return true;
        }

        // Guardian can only view their own student's billings
        if ($user->role === 'WALI_SANTRI') {
            return $billing->student->guardian_id === $user->guardian?->id;
        }

        return false;
    }

    /**
     * Only admin can create billing
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }

    /**
     * Cannot edit paid/locked billings
     */
    public function update(User $user, Billing $billing): bool
    {
        if ($billing->status === 'PAID') {
            return false;
        }

        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }

    /**
     * Cannot delete paid billings
     */
    public function delete(User $user, Billing $billing): bool
    {
        if ($billing->status === 'PAID') {
            return false;
        }

        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }
}

<?php

namespace App\Policies;

use App\Models\Billing;
use App\Models\User;

class BillingPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU', 'WALI_SANTRI', 'KANTOR_SANTRI']);
    }

    public function view(User $user, Billing $billing): bool
    {
        if ($user->role === 'SUPER_ADMIN') {
            return true;
        }

        if ($user->role === 'ADMIN_TU') {
            return true;
        }

        if ($user->role === 'WALI_SANTRI') {
            return $billing->student->guardian_id === $user->guardian?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }

    public function update(User $user, Billing $billing): bool
    {
        if ($billing->status === 'PAID') {
            return false;
        }

        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }

    public function delete(User $user, Billing $billing): bool
    {
        if ($billing->status === 'PAID') {
            return false;
        }

        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }

    public function restore(User $user, Billing $billing): bool
    {
        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }

    public function forceDelete(User $user, Billing $billing): bool
    {
        return in_array($user->role, ['SUPER_ADMIN', 'ADMIN_TU']);
    }
}

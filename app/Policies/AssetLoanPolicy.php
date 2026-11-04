<?php

namespace App\Policies;

use App\Enums\LoanStatus;
use App\Models\AssetLoan;
use App\Models\User;

class AssetLoanPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active;
    }

    public function view(User $user, AssetLoan $loan): bool
    {
        return $user->is_active && ($user->isStaff() || $loan->borrower_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->isStaff();
    }

    public function return(User $user, AssetLoan $loan): bool
    {
        return $user->is_active
            && $user->isStaff()
            && $loan->status === LoanStatus::Dipinjam;
    }

    public function delete(User $user, AssetLoan $loan): bool
    {
        return $user->is_active && $user->isAdmin();
    }
}
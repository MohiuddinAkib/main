<?php

namespace App\Domain\Wallet\Policies;

use App\Domain\Currency\Projections\Denomination;
use App\Domain\Wallet\Projections\Wallet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class WalletPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wallet $wallet): bool|Response
    {
        if ($wallet->user->isNot($user)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function show(User $user, Wallet $wallet): bool|Response
    {
        if ($wallet->user->isNot($user)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wallet $wallet): bool|Response
    {
        if ($wallet->user->isNot($user)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function removeDenomination(User $user, Wallet $wallet, Denomination $denomination): bool|Response
    {
        if ($wallet->user->isNot($user) || $denomination->wallet->isNot($wallet)) {
            return Response::denyAsNotFound();
        }

        if($denomination->quantity > 0) {
            return Response::deny("The denomination balance is not empty.");
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CodingChallenge;
use Illuminate\Auth\Access\HandlesAuthorization;

class CodingChallengePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CodingChallenge');
    }

    public function view(AuthUser $authUser, CodingChallenge $codingChallenge): bool
    {
        return $authUser->can('View:CodingChallenge');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CodingChallenge');
    }

    public function update(AuthUser $authUser, CodingChallenge $codingChallenge): bool
    {
        return $authUser->can('Update:CodingChallenge');
    }

    public function delete(AuthUser $authUser, CodingChallenge $codingChallenge): bool
    {
        return $authUser->can('Delete:CodingChallenge');
    }

    public function restore(AuthUser $authUser, CodingChallenge $codingChallenge): bool
    {
        return $authUser->can('Restore:CodingChallenge');
    }

    public function forceDelete(AuthUser $authUser, CodingChallenge $codingChallenge): bool
    {
        return $authUser->can('ForceDelete:CodingChallenge');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CodingChallenge');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CodingChallenge');
    }

    public function replicate(AuthUser $authUser, CodingChallenge $codingChallenge): bool
    {
        return $authUser->can('Replicate:CodingChallenge');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CodingChallenge');
    }

}
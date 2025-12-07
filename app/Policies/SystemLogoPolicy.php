<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SystemLogo;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemLogoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SystemLogo');
    }

    public function view(AuthUser $authUser, SystemLogo $systemLogo): bool
    {
        return $authUser->can('View:SystemLogo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SystemLogo');
    }

    public function update(AuthUser $authUser, SystemLogo $systemLogo): bool
    {
        return $authUser->can('Update:SystemLogo');
    }

    public function delete(AuthUser $authUser, SystemLogo $systemLogo): bool
    {
        return $authUser->can('Delete:SystemLogo');
    }

    public function restore(AuthUser $authUser, SystemLogo $systemLogo): bool
    {
        return $authUser->can('Restore:SystemLogo');
    }

    public function forceDelete(AuthUser $authUser, SystemLogo $systemLogo): bool
    {
        return $authUser->can('ForceDelete:SystemLogo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SystemLogo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SystemLogo');
    }

    public function replicate(AuthUser $authUser, SystemLogo $systemLogo): bool
    {
        return $authUser->can('Replicate:SystemLogo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SystemLogo');
    }

}
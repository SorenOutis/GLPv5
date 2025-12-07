<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CommunityPost;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityPostPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommunityPost');
    }

    public function view(AuthUser $authUser, CommunityPost $communityPost): bool
    {
        return $authUser->can('View:CommunityPost');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommunityPost');
    }

    public function update(AuthUser $authUser, CommunityPost $communityPost): bool
    {
        return $authUser->can('Update:CommunityPost');
    }

    public function delete(AuthUser $authUser, CommunityPost $communityPost): bool
    {
        return $authUser->can('Delete:CommunityPost');
    }

    public function restore(AuthUser $authUser, CommunityPost $communityPost): bool
    {
        return $authUser->can('Restore:CommunityPost');
    }

    public function forceDelete(AuthUser $authUser, CommunityPost $communityPost): bool
    {
        return $authUser->can('ForceDelete:CommunityPost');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommunityPost');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommunityPost');
    }

    public function replicate(AuthUser $authUser, CommunityPost $communityPost): bool
    {
        return $authUser->can('Replicate:CommunityPost');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommunityPost');
    }

}
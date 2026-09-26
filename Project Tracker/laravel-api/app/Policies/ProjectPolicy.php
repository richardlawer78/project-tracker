<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\ProjectAccess;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return ProjectAccess::canView($user, $project);
    }

    public function create(User $user): bool
    {
        return ProjectAccess::canCreate($user);
    }

    public function update(User $user, Project $project): bool
    {
        return ProjectAccess::canManage($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return ProjectAccess::canManage($user, $project);
    }

    public function restore(User $user, Project $project): bool
    {
        return ProjectAccess::canManage($user, $project);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return ProjectAccess::canManage($user, $project);
    }
}

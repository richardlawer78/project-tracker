<?php

namespace App;

use App\Models\Project;
use App\Models\User;

class ProjectAccess
{
    public static function canView(User $user, Project $project): bool
    {
        return $user->role === 'admin'
            || $project->owner_id === $user->id
            || $project->members()
                ->where('users.id', $user->id)
                ->exists();
    }

    public static function canManage(User $user, Project $project): bool
    {
        return $user->role === 'admin'
            || $project->owner_id === $user->id;
    }
}
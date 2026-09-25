<?php

namespace App;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ProjectAccess
{
    /**
     * Who may create new projects.
     * Change the roles in this list if you want to open it up (or lock it down).
     */
    public static function canCreate(?User $user): bool
    {
        return $user !== null
            && in_array($user->role, ['admin', 'project-manager'], true);
    }

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

    /**
     * IDs of the projects this person may open.
     * Returns null for admins, which means "no limit - all projects".
     *
     * @return array<int, int>|null
     */
    public static function visibleProjectIds(User $user): ?array
    {
        if ($user->role === 'admin') {
            return null;
        }

        return Project::query()
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('members', fn ($members) => $members->where('users.id', $user->id));
            })
            ->pluck('id')
            ->all();
    }

    /**
     * Limit any query on a table that has a project_id column
     * to the projects this person is allowed to open.
     */
    public static function limitToVisible(Builder $query, User $user, string $column = 'project_id'): Builder
    {
        $ids = self::visibleProjectIds($user);

        return $ids === null ? $query : $query->whereIn($column, $ids);
    }

    /**
     * Items that belong to a project follow that project's rules.
     * Items that belong to no project are admin-only.
     */
    public static function canViewItem(User $user, ?Project $project): bool
    {
        return $project ? self::canView($user, $project) : $user->role === 'admin';
    }

    public static function canManageItem(User $user, ?Project $project): bool
    {
        return $project ? self::canManage($user, $project) : $user->role === 'admin';
    }

    /**
     * Team members can add items to projects they belong to.
     */
    public static function canCreateIn(User $user, ?Project $project): bool
    {
        return self::canViewItem($user, $project);
    }
}

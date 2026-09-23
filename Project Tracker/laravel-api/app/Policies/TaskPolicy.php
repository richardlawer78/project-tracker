<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\ProjectAccess;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return ProjectAccess::canView($user, $task->project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return ProjectAccess::canManage($user, $task->project);
    }

    public function delete(User $user, Task $task): bool
    {
        return ProjectAccess::canManage($user, $task->project);
    }

    public function restore(User $user, Task $task): bool
    {
        return ProjectAccess::canManage($user, $task->project);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return ProjectAccess::canManage($user, $task->project);
    }
}
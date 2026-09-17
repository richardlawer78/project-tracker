<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackerPresenter
{
    public static function value(Model $item, string $column): string
    {
        $value = match ($column) {
            'project' => $item->project->name ?? '—',
            'user' => $item->user->name ?? '—',
            'assignee' => $item->assignee->name ?? 'Unassigned',
            default => $item->{$column},
        };

        if ($value instanceof \DateTimeInterface) {
            return $value->format('M j, Y');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        if ($value === null || $value === '') {
            return '—';
        }

        return Str::limit((string) $value, 80);
    }
}

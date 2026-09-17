<?php

namespace App\Support;

class TrackerFields
{
    /**
     * @return array<int, string>
     */
    public static function options(string $feature, string $field): array
    {
        return match ([$feature, $field]) {
            ['kickoff', 'status'] => ['scheduled', 'completed'],
            ['sprints', 'status'] => ['planned', 'active', 'completed'],
            ['backlog', 'status'] => ['backlog', 'ready', 'in-progress'],
            ['backlog', 'type'] => ['epic', 'feature', 'story'],
            ['tasks', 'status'] => ['pending', 'in-progress', 'completed'],
            ['milestones', 'status'] => ['upcoming', 'pending', 'in-progress', 'completed'],
            ['testing', 'status'] => ['pending', 'passed', 'failed'],
            ['testing', 'type'] => ['functional', 'performance', 'ui'],
            ['risks', 'status'] => ['open', 'mitigating', 'mitigated', 'closed'],
            ['risks', 'category'] => ['resource', 'scope', 'technical', 'external', 'financial'],
            ['budget', 'status'] => ['on-track', 'under', 'over'],
            ['changes', 'status'] => ['pending', 'approved', 'rejected'],
            ['changes', 'type'] => ['feature', 'schedule', 'scope', 'budget'],
            ['documents', 'type'] => ['pdf', 'doc', 'excel', 'design', 'other'],
            ['lessons', 'category'] => ['process', 'technical', 'team', 'scope', 'quality'],
            ['lessons', 'impact'] => ['positive', 'negative'],
            ['definitions', 'list_type'] => ['dor', 'dod'],
            default => match ($field) {
                'priority', 'influence', 'interest', 'probability', 'impact' => ['low', 'medium', 'high'],
                default => ['low', 'medium', 'high'],
            },
        };
    }
}

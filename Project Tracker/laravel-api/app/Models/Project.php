<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'client',
        'team',
        'project_type',
        'status',
        'priority',
        'start_date',
        'end_date',
        'budget',
        'spent',
        'progress',
        'settings',
        'methodology', 'sprint_duration', 'sprint_goal', 'velocity', 'phases', 'milestones_text', 'deliverables',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'spent' => 'decimal:2',
            'settings' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function sprints(): HasMany { return $this->hasMany(Sprint::class); }
    public function backlogItems(): HasMany { return $this->hasMany(BacklogItem::class); }
    public function workflows(): HasMany { return $this->hasMany(Workflow::class); }
    public function milestones(): HasMany { return $this->hasMany(Milestone::class); }
    public function budgetItems(): HasMany { return $this->hasMany(BudgetItem::class); }
    public function timeEntries(): HasMany { return $this->hasMany(TimeEntry::class); }
    public function projectResources(): HasMany { return $this->hasMany(ProjectResource::class); }
    public function stakeholders(): HasMany { return $this->hasMany(Stakeholder::class); }
    public function kickoffs(): HasMany { return $this->hasMany(Kickoff::class); }
    public function dorDodItems(): HasMany { return $this->hasMany(DorDodItem::class); }
    public function testCases(): HasMany { return $this->hasMany(TestCase::class); }
    public function risks(): HasMany { return $this->hasMany(Risk::class); }
    public function changeLogEntries(): HasMany { return $this->hasMany(ChangeLogEntry::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }
    public function lessonsLearned(): HasMany { return $this->hasMany(LessonLearned::class); }
    public function chatChannels(): HasMany { return $this->hasMany(ChatChannel::class); }
}

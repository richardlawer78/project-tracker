<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'job_title',
    'avatar',
    'availability_percent',
    'must_change_password',
    'temporary_password_expires_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'boolean',
        'temporary_password_expires_at' => 'datetime',
    ];
}
    public function projects(): BelongsToMany
{
    return $this->belongsToMany(Project::class, 'project_members')
        ->withPivot('role')
        ->withTimestamps();
}
    

    public function assignedTasks(): HasMany { return $this->hasMany(Task::class, 'assigned_to'); }
    public function timeEntries(): HasMany { return $this->hasMany(TimeEntry::class); }
    public function ownedRisks(): HasMany { return $this->hasMany(Risk::class, 'owner_id'); }
    public function requestedChangeLogs(): HasMany { return $this->hasMany(ChangeLogEntry::class, 'requestor_id'); }
    public function uploadedDocuments(): HasMany { return $this->hasMany(Document::class, 'uploaded_by'); }
    public function chatMessages(): HasMany { return $this->hasMany(ChatMessage::class); }
    public function projectResources(): HasMany { return $this->hasMany(ProjectResource::class); }
    public function ownedProjects(): HasMany { return $this->hasMany(Project::class, 'owner_id'); }
}

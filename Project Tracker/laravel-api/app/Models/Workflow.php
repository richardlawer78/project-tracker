<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workflow extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['stages' => 'array']; }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}

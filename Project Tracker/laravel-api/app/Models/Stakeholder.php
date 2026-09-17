<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stakeholder extends Model
{
    /** @use HasFactory<\Database\Factories\StakeholderFactory> */
    use HasFactory;
    protected $guarded = [];
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}

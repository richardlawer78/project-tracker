<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
    /** @use HasFactory<\Database\Factories\RiskFactory> */
    use HasFactory;
    protected $guarded = [];
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
}

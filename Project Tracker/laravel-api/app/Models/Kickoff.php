<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kickoff extends Model
{
    /** @use HasFactory<\Database\Factories\KickoffFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['date' => 'date']; }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function objectives(): HasMany { return $this->hasMany(KickoffObjective::class); }
}

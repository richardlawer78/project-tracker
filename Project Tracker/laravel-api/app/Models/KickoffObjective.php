<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KickoffObjective extends Model
{
    /** @use HasFactory<\Database\Factories\KickoffObjectiveFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['completed' => 'boolean']; }
    public function kickoff(): BelongsTo { return $this->belongsTo(Kickoff::class); }
}

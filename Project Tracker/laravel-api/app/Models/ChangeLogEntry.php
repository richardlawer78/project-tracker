<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeLogEntry extends Model
{
    /** @use HasFactory<\Database\Factories\ChangeLogEntryFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['date' => 'date']; }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function requestor(): BelongsTo { return $this->belongsTo(User::class, 'requestor_id'); }
}

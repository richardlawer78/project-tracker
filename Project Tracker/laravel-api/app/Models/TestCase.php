<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestCase extends Model
{
    /** @use HasFactory<\Database\Factories\TestCaseFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['last_run' => 'date']; }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}

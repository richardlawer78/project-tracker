<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetItemFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['allocated' => 'decimal:2', 'spent' => 'decimal:2']; }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}

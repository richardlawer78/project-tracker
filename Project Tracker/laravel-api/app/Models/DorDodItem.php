<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DorDodItem extends Model
{
    /** @use HasFactory<\Database\Factories\DorDodItemFactory> */
    use HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['checked' => 'boolean']; }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}

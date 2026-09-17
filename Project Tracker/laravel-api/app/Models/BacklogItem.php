<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BacklogItem extends Model
{
    /** @use HasFactory<\Database\Factories\BacklogItemFactory> */
    use HasFactory;
    protected $guarded = [];
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}

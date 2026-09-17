<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LessonLearned extends Model { use HasFactory; protected $table = 'lessons_learned'; protected $guarded = []; protected function casts(): array { return ['date' => 'date']; } public function project(): BelongsTo { return $this->belongsTo(Project::class); } }

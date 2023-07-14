<?php

namespace App\Models;

use App\Enums\CreationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Card extends Model
{
    protected $fillable = [
        'summary',
        'chapter',
        'intro_card',
        'source_text',
        'position',
        'page_number',
        'creation_type',
        'course_id',
        'lesson_id',
    ];

    use HasFactory;

    protected $casts = [
      'intro_card' => 'bool',
      'creation_type' => CreationType::class,
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function getChapter(): ?string
    {
        return $this->chapter;
    }

    public function isIntroCard(): bool
    {
        return $this->intro_card;
    }

    public function getSourceText(): ?string
    {
        return $this->source_text;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getPageNumber(): ?int
    {
        return $this->page_number;
    }

    public function getCreationType(): CreationType
    {
        return $this->creation_type;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'author',
        'year',
        'summary',
        'cover_image',
        'cover_color',
        'archived',
        'user_id',
    ];

    protected $casts = [
      'archived' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function getCoverImage(): ?string
    {
        return $this->cover_image;
    }

    public function getCoverColor(): ?string
    {
        return $this->cover_color;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }
}

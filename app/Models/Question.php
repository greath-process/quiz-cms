<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
      'type',
      'text',
      'card_id',
      'sort'
    ];

    protected $casts = [
      'type' => QuestionType::class,
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): QuestionType
    {
        return $this->type;
    }

    public function getText(): string
    {
        return $this->text;
    }
}

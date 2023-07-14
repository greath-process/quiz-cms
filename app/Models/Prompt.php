<?php

namespace App\Models;

use App\Enums\CreationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'prompt',
        'model_type',
        'quiz_type',
    ];

    public function getName(): int
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    public function getQuizType(): ?string
    {
        return $this->quiz_type;
    }
}

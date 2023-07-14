<?php

namespace App\Enums;

enum QuestionType: int
{
    use EnumToArray;

    case TRUEFALSE = 1;
    case BLANK = 2;
    case CHOICE = 3;
    case MIX = 4;


    public static function fromName(string $name): ?int
    {
        foreach (self::array() as $constantName => $constantValue) {
            if (strtoupper($constantValue) == strtoupper($name)) {
                return $constantName;
            }
        }

        return null;
    }

    public static function fromValue(int $value): ?string
    {
        foreach (self::array() as $constantName => $constantValue) {
            if (strtoupper($constantName) == strtoupper($value)) {
                return $constantValue;
            }
        }

        return null;
    }
}

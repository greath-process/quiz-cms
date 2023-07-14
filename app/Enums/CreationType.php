<?php

namespace App\Enums;

enum CreationType: int
{
    use EnumToArray;

    case MANUAL = 1;
    case TEXT = 2;
    case HIGHLIGHT = 3;
}

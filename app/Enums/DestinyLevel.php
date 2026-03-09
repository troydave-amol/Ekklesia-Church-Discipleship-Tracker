<?php

namespace App\Enums;

enum DestinyLevel: string
{
    case LifeClass = 'life_class';
    case Sol1      = 'sol1';
    case Sol2      = 'sol2';

    public function label(): string
    {
        return match($this) {
            self::LifeClass => 'Life Class',
            self::Sol1      => 'SOL 1',
            self::Sol2      => 'SOL 2',
        };
    }
}
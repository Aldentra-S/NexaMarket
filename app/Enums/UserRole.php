<?php

namespace App\Enums;

enum UserRole: string
{
    case BUYER = 'buyer';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::BUYER => 'Pembeli',
            self::ADMIN => 'Administrator',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::BUYER => 'bg-blue-100 text-blue-800 border-blue-200',
            self::ADMIN => 'bg-purple-100 text-purple-800 border-purple-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

namespace App\Enums;

enum ProductStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::PUBLISHED => 'Ditayangkan',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-amber-100 text-amber-800 border-amber-200',
            self::PUBLISHED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::ARCHIVED => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

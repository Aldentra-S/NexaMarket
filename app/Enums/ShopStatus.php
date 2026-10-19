<?php

namespace App\Enums;

enum ShopStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::SUSPENDED => 'Ditangguhkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::REJECTED => 'bg-red-100 text-red-800 border-red-200',
            self::SUSPENDED => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

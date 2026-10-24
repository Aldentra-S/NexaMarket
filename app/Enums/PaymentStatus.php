<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::SUCCESS => 'Berhasil',
            self::FAILED => 'Gagal',
            self::EXPIRED => 'Kedaluwarsa',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::SUCCESS => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::FAILED => 'bg-rose-100 text-rose-800 border-rose-200',
            self::EXPIRED => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

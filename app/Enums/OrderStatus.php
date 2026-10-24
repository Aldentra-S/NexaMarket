<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Sudah Dibayar',
            self::CANCELLED => 'Dibatalkan',
            self::EXPIRED => 'Kedaluwarsa',
            self::COMPLETED => 'Selesai',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::PAID => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::CANCELLED => 'bg-rose-100 text-rose-800 border-rose-200',
            self::EXPIRED => 'bg-gray-100 text-gray-700 border-gray-200',
            self::COMPLETED => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

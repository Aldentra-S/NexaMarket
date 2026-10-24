<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Sudah Dibayar',
            self::PROCESSING => 'Sedang Diproses',
            self::SHIPPED => 'Sedang Dikirim',
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
            self::PROCESSING => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::SHIPPED => 'bg-purple-100 text-purple-800 border-purple-200',
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

    public function isProcessing(): bool
    {
        return $this === self::PROCESSING;
    }

    public function isShipped(): bool
    {
        return $this === self::SHIPPED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * Get valid subsequent statuses that a seller can transition to during fulfillment.
     *
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PAID => [self::PROCESSING],
            self::PROCESSING => [self::SHIPPED],
            self::SHIPPED => [self::COMPLETED],
            default => [],
        };
    }

    /**
     * Determine whether a transition to the target status is allowed.
     */
    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

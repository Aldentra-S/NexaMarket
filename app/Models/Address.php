<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'address_line',
        'delivery_notes',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user that owns this shipping address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Make this address the default address for the user, resetting previous defaults.
     */
    public function makeDefault(): void
    {
        DB::transaction(function () {
            static::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);

            $this->update(['is_default' => true]);
        });
    }

    /**
     * Get a human-readable complete formatted address string.
     */
    public function formattedAddress(): string
    {
        $parts = [
            $this->address_line,
            $this->village ? 'Kel. ' . $this->village : null,
            'Kec. ' . $this->district,
            $this->city,
            $this->province,
            $this->postal_code,
        ];

        return implode(', ', array_filter($parts));
    }
}


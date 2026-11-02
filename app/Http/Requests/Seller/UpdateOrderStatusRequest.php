<?php

namespace App\Http\Requests\Seller;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::enum(OrderStatus::class),
            ],
            'courier' => [
                Rule::requiredIf(fn () => $this->status === OrderStatus::SHIPPED->value && ($this->has('courier') || $this->has('tracking_number'))),
                'nullable',
                'string',
                'max:50',
            ],
            'tracking_number' => [
                Rule::requiredIf(fn () => $this->status === OrderStatus::SHIPPED->value && ($this->has('courier') || $this->has('tracking_number'))),
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'status pesanan',
            'courier' => 'kurir pengiriman',
            'tracking_number' => 'nomor resi',
        ];
    }
}

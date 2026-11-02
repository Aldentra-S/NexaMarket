<?php

namespace App\Http\Requests\Buyer;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'village' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'label' => 'label alamat',
            'recipient_name' => 'nama penerima',
            'phone' => 'nomor telepon penerima',
            'province' => 'provinsi',
            'city' => 'kota / kabupaten',
            'district' => 'kecamatan',
            'village' => 'kelurahan / desa',
            'postal_code' => 'kode pos',
            'address_line' => 'detail alamat',
            'delivery_notes' => 'catatan pengiriman',
            'is_default' => 'alamat utama',
        ];
    }
}


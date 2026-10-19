<?php

namespace App\Http\Requests\Buyer;

use App\Models\Shop;
use Illuminate\Foundation\Http\FormRequest;

class ApplyShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->can('create', Shop::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $existingShopId = $this->user()?->shop?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                $existingShopId ? 'unique:shops,name,' . $existingShopId : 'unique:shops,name',
            ],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama toko wajib diisi.',
            'name.min' => 'Nama toko minimal 3 karakter.',
            'name.max' => 'Nama toko maksimal 100 karakter.',
            'name.unique' => 'Nama toko sudah digunakan oleh toko lain.',
            'description.required' => 'Deskripsi toko wajib diisi.',
            'description.min' => 'Deskripsi toko minimal 10 karakter.',
            'description.max' => 'Deskripsi toko maksimal 1000 karakter.',
        ];
    }
}

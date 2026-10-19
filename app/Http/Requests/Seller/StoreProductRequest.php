<?php

namespace App\Http\Requests\Seller;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'price' => ['required', 'numeric', 'min:100', 'max:999999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
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
            'name.required' => 'Nama produk wajib diisi.',
            'name.min' => 'Nama produk minimal 3 karakter.',
            'name.max' => 'Nama produk maksimal 150 karakter.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'description.min' => 'Deskripsi produk minimal 10 karakter.',
            'description.max' => 'Deskripsi produk maksimal 5000 karakter.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga produk harus berupa angka valid.',
            'price.min' => 'Harga produk minimal Rp 100.',
            'price.max' => 'Harga produk melebihi batas maksimum.',
            'stock.required' => 'Jumlah stok produk wajib diisi.',
            'stock.integer' => 'Stok produk harus berupa bilangan bulat.',
            'stock.min' => 'Stok produk minimal 0.',
            'stock.max' => 'Stok produk melebihi batas maksimum 999.999 unit.',
            'status.required' => 'Status publikasi produk wajib dipilih.',
            'status.enum' => 'Status produk yang dipilih tidak valid.',
        ];
    }
}

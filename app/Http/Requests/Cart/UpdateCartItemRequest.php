<?php

namespace App\Http\Requests\Cart;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCartItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $item = $this->route('item');

        return $this->user() !== null && ($item instanceof CartItem ? $this->user()->can('update', $item) : true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $item = $this->route('item');
            $requestedQty = (int) $this->input('quantity', 1);

            if (! $item instanceof CartItem || $requestedQty < 1) {
                return;
            }

            $product = $item->product;
            if (! $product) {
                $validator->errors()->add('quantity', 'Produk tidak ditemukan.');
                return;
            }

            if ($product->status !== ProductStatus::PUBLISHED) {
                $validator->errors()->add('quantity', 'Produk sedang tidak aktif atau tidak tersedia untuk dibeli.');
                return;
            }

            $shop = $product->shop;
            if (! $shop || $shop->status !== ShopStatus::APPROVED || $shop->isSuspended()) {
                $validator->errors()->add('quantity', 'Toko produk sedang tidak aktif atau ditangguhkan.');
                return;
            }

            if ($requestedQty > $product->stock) {
                $validator->errors()->add(
                    'quantity',
                    "Stok produk tidak mencukupi. Tersedia {$product->stock} unit."
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Jumlah kuantitas wajib diisi.',
            'quantity.integer' => 'Kuantitas harus berupa bilangan bulat.',
            'quantity.min' => 'Kuantitas minimal adalah 1.',
        ];
    }
}

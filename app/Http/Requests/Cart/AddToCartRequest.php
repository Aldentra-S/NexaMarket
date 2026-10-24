<?php

namespace App\Http\Requests\Cart;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AddToCartRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $productId = $this->input('product_id');
            $requestedQty = (int) $this->input('quantity', 1);

            if (! $productId || $requestedQty < 1) {
                return;
            }

            $product = Product::with('shop')->find($productId);
            if (! $product) {
                $validator->errors()->add('product_id', 'Produk tidak ditemukan.');
                return;
            }

            if ($product->status !== ProductStatus::PUBLISHED) {
                $validator->errors()->add('product_id', 'Produk sedang tidak aktif atau tidak tersedia untuk dibeli.');
                return;
            }

            $shop = $product->shop;
            if (! $shop || $shop->status !== ShopStatus::APPROVED || $shop->isSuspended()) {
                $validator->errors()->add('product_id', 'Toko produk sedang tidak aktif atau ditangguhkan.');
                return;
            }

            // Check existing cart quantity for this user
            $existingQty = 0;
            $user = $this->user();
            if ($user) {
                $cart = \App\Models\Cart::where('user_id', $user->id)->first();
                if ($cart) {
                    $existingItem = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $product->id)
                        ->first();
                    if ($existingItem) {
                        $existingQty = $existingItem->quantity;
                    }
                }
            }


            $totalRequested = $existingQty + $requestedQty;

            if ($totalRequested > $product->stock) {
                $validator->errors()->add(
                    'quantity',
                    "Stok produk tidak mencukupi. Tersedia {$product->stock} unit, sedangkan total permintaan keranjang menjadi {$totalRequested} unit."
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
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk tidak valid atau tidak ditemukan.',
            'quantity.required' => 'Jumlah kuantitas wajib diisi.',
            'quantity.integer' => 'Kuantitas harus berupa bilangan bulat.',
            'quantity.min' => 'Kuantitas minimal adalah 1.',
        ];
    }
}

<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProductImagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $this->user() !== null && $product !== null && $this->user()->can('update', $product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order' => ['nullable', 'array'],
            'order.*' => ['integer'],
            'orders' => ['nullable', 'array'],
            'orders.*.id' => ['required_with:orders', 'integer'],
            'orders.*.sort_order' => ['required_with:orders', 'integer', 'min:0'],
        ];
    }
}

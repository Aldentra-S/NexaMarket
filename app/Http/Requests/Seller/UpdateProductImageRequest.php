<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');
        $image = $this->route('image');

        return $this->user() !== null 
            && $product !== null 
            && $image !== null 
            && $image->product_id === $product->id 
            && $this->user()->can('update', $product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
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
            'image.required' => 'Berkas gambar pengganti wajib dipilih.',
            'image.image' => 'Berkas harus berupa gambar valid.',
            'image.mimes' => 'Format gambar harus berupa JPEG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}

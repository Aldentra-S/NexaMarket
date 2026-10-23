<?php

namespace App\Http\Requests\Seller;

use App\Models\ProductImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductImageRequest extends FormRequest
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
            'images' => ['nullable', 'array', 'max:' . ProductImage::MAX_PER_PRODUCT],
            'images.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $product = $this->route('product');
            if (! $product) {
                return;
            }

            $hasImages = $this->hasFile('images');
            $hasImage = $this->hasFile('image');

            if (! $hasImages && ! $hasImage) {
                $validator->errors()->add('images', 'Pilih minimal satu berkas gambar untuk diunggah.');
                return;
            }

            $newCount = 0;
            if ($hasImages) {
                $newCount += count((array) $this->file('images'));
            }
            if ($hasImage) {
                $newCount += 1;
            }

            $currentCount = $product->images()->count();
            if ($currentCount + $newCount > ProductImage::MAX_PER_PRODUCT) {
                $validator->errors()->add(
                    'images',
                    'Jumlah gambar melebihi batas maksimal (' . ProductImage::MAX_PER_PRODUCT . ' gambar per produk).'
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
            'images.array' => 'Format daftar gambar tidak valid.',
            'images.max' => 'Maksimal ' . ProductImage::MAX_PER_PRODUCT . ' gambar dapat diunggah sekaligus.',
            'images.*.image' => 'Berkas harus berupa gambar valid.',
            'images.*.mimes' => 'Format gambar harus berupa JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran gambar maksimal 2MB per berkas.',
            'image.image' => 'Berkas harus berupa gambar valid.',
            'image.mimes' => 'Format gambar harus berupa JPEG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}

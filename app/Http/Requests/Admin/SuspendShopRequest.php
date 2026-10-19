<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SuspendShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'suspension_reason' => ['required', 'string', 'min:5', 'max:1000'],
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
            'suspension_reason.required' => 'Alasan penangguhan toko wajib diisi.',
            'suspension_reason.min' => 'Alasan penangguhan minimal 5 karakter.',
            'suspension_reason.max' => 'Alasan penangguhan maksimal 1000 karakter.',
        ];
    }
}

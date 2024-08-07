<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'category_id' => 'required',
            'name' => 'required|unique:products',
            'price' => 'required',
            'description' => 'nullable',
            'image_path' => 'nullable|image',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|integer',
            'galleries' => 'required|array',
            'galleries.*' => 'required|image',
        ];
    }
}

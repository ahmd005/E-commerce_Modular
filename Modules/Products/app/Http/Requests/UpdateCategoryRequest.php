<?php

namespace Modules\Products\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category'); // استخراج الـ ID من الرابط
        return [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $categoryId,
          //  'description' => 'nullable|string',
        ];
    }
}
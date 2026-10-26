<?php

namespace Modules\Orders\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_uuid' => 'required|uuid|exists:products,uuid',
            'quantity'     => 'required|integer|min:1',
        ];
    }
}

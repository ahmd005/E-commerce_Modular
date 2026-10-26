<?php

namespace Modules\Orders\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // عند عدم إرسال cart_item_ids يتم إنشاء الطلب من كامل السلة
    public function rules(): array
    {
        return [
            'cart_item_ids'   => 'sometimes|array|min:1',
            'cart_item_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('cart_items', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function cartItemIds(): array
    {
        return array_map('intval', $this->validated()['cart_item_ids'] ?? []);
    }
}

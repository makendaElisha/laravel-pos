<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $product_id = $this->route('product')->id;
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products')->ignore($product_id),
            ],
            'buy_price' => 'nullable|numeric',
            'sell_price' => 'required|numeric',
            'quantity' => 'nullable|integer',
            'items_in_box' => 'nullable|integer',
        ];
    }
}

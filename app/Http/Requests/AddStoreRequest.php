<?php

namespace App\Http\Requests;

use App\Models\Enums\StoreTypesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddStoreRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'postcode' => ['required', 'string', 'exists:postcodes,postcode'],
            'store_type' => ['required', Rule::enum(StoreTypesEnum::class)],
            'is_open' => ['required', 'boolean'],
            'max_delivery_distance' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'postcode.exists' => 'Issues with postcode. Please recheck and ensure it is without spaces.',
        ];
    }
}

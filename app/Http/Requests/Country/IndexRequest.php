<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    private const ALLOWED_SORT_FIELDS = ['name', 'official_name', 'cca3', 'cca2', 'gini', 'hdi'];
    private const ALLOWED_SORT_ORDERS = ['asc', 'desc'];
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Illuminate\Contracts\Validation/ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string|in:' . implode(',', self::ALLOWED_SORT_FIELDS),
            'sort_order' => 'nullable|string|in:' . implode(',', self::ALLOWED_SORT_ORDERS),
        ];
    }
}

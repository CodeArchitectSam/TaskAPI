<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListTaskRequest extends FormRequest
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
            'status' => 'sometimes|in:pending,in-progress,completed',
            'due_date' => 'sometimes|date|date_format:Y-m-d',
            'due_date_from' => 'sometimes|date|date_format:Y-m-d',
            'due_date_to' => 'sometimes|date|date_format:Y-m-d|after_or_equal:due_date_from',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'sort_by' => 'sometimes|in:due_date,created_at',
            'sort_order' => 'sometimes|in:asc,desc',
        ];
    }
}

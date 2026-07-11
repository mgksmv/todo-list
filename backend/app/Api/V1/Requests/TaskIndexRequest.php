<?php

namespace App\Api\V1\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer'],
            'sort_field' => ['sometimes', 'string'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc'],
            'title' => ['sometimes', 'string'],
            'user_name' => ['sometimes', 'string'],
            'due_date' => ['sometimes', 'date'],
            'created_at' => ['sometimes', 'date'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
        ];
    }
}

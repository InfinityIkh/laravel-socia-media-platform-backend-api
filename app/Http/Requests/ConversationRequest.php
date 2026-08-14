<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'public',
                    'private',
                ]),
            ],

            'user_ids' => [
                'required',
                'array',
                'min:2',
            ],

            'user_ids.*' => [
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ];
    }
}
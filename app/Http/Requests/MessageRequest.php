<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [
        'conversation_id' => [
            'required',
            'integer',
            'exists:conversations,id',
        ],

        'type' => [
            'required',
            Rule::in(['text', 'image', 'video', 'document']),
        ],

        'body' => [
            'nullable',
            'string',
            'max:5000',
            'required_if:type,text',
        ],

        'media' => [
            'required_unless:type,text',
            'nullable',
            'file',
            'max:51200',

            Rule::when(
                $this->type === 'image',
                ['mimes:jpg,jpeg,png,webp']
            ),

            Rule::when(
                $this->type === 'video',
                ['mimes:mp4,mov,avi,webm']
            ),

            Rule::when(
                $this->type === 'document',
                ['mimes:pdf,doc,docx,txt']
            ),
        ],
    ];
    }
}

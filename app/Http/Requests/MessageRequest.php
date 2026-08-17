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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [

        'body' => [
            'nullable',
            'string',
            'max:5000',
            'required_without:media',
        ],

        'media' => [
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

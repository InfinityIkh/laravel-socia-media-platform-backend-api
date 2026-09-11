<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        if ($this->is('*/login')) {
            return [
                'email' => ['required', 'email'],
                'password' => ['required','min:8','max:255'],
            ];
        }
        $userId = $this->route('user')?->id;
        return [
            'name' => [$this->isMethod('post') ? 'required' : 'nullable','min:5','max:255'],
            'email' => [$this->isMethod('post') ? 'required' : 'nullable','email', Rule::unique('users', 'email')->ignore($userId),],
            'image' => ['nullable','image','mimes:jpeg,jpg,png,webp','max:2048'],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable' ,
                'min:8',
                'max:255'
            ],
        ];
    }
}

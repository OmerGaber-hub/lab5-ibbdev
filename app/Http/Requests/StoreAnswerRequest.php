<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['body' => 'required|string|min:5'];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'نص الإجابة مطلوب.',
            'body.min' => 'الإجابة يجب أن تكون 5 أحرف على الأقل.',
        ];
    }
}
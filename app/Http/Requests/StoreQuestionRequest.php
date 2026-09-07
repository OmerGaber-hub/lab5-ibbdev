<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان السؤال مطلوب.',
            'body.required' => 'نص السؤال مطلوب.',
            'body.min' => 'نص السؤال يجب أن يكون 10 أحرف على الأقل.',
            'image.image' => 'الملف المرفق يجب أن يكون صورة.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ];
    }
}
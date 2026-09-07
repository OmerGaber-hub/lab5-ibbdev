@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">سؤال جديد</h1>

    <form method="POST" action="{{ route('questions.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">العنوان</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded p-2">
            @error('title') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">تفاصيل السؤال</label>
            <textarea name="body" rows="5" class="w-full border rounded p-2">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">صورة الخطأ (اختياري)</label>
            <input type="file" name="image" class="w-full">
            @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button class="bg-indigo-600 text-white px-4 py-2 rounded">نشر السؤال</button>
    </form>
@endsection
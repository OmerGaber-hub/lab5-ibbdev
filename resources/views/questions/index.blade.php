@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">الأسئلة</h1>

    <div class="space-y-4">
        @forelse ($questions as $question)
            <a href="{{ route('questions.show', $question) }}"
               class="block bg-white p-4 rounded shadow-sm hover:shadow-md">
                <h2 class="text-lg font-semibold text-indigo-600">{{ $question->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    بواسطة {{ $question->user->username }} — {{ $question->answers_count }} إجابة
                </p>
            </a>
        @empty
            <p class="text-gray-500">لا توجد أسئلة بعد. كن أول من يطرح سؤالاً!</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $questions->links() }}</div>
@endsection
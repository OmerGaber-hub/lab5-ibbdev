@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-2">{{ $question->title }}</h1>
    <p class="text-sm text-gray-500 mb-4">بواسطة {{ $question->user->username }}</p>

    <div class="bg-white p-4 rounded shadow-sm mb-6">
        <p>{{ $question->body }}</p>

        @if ($question->image)
            <img src="{{ asset('storage/'.$question->image) }}" class="mt-4 rounded max-w-md">
        @endif
    </div>

    <h2 class="text-xl font-semibold mb-4">الإجابات ({{ $question->answers->count() }})</h2>

    <div class="space-y-4 mb-8">
        @forelse ($question->answers as $answer)
            <div class="bg-white p-4 rounded shadow-sm {{ $answer->is_accepted ? 'border-2 border-green-500' : '' }}">
                <p>{{ $answer->body }}</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-sm text-gray-500">بواسطة {{ $answer->user->username }}</span>

                    @if ($answer->is_accepted)
                        <span class="text-green-600 text-sm font-semibold">✔ إجابة معتمدة</span>
                    @elseif (auth()->check() && auth()->id() === $question->user_id)
                        <form method="POST" action="{{ route('answers.accept', $answer) }}">
                            @csrf
                            <button class="text-sm text-indigo-600">اعتماد كحل</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500">لا توجد إجابات بعد.</p>
        @endforelse
    </div>

    @auth
        <form method="POST" action="{{ route('answers.store', $question) }}" class="space-y-3">
            @csrf
            <textarea name="body" rows="4" placeholder="اكتب إجابتك..." class="w-full border rounded p-2">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">إرسال الإجابة</button>
        </form>
    @else
        <p class="text-gray-500">
            <a href="{{ route('login') }}" class="text-indigo-600">سجّل دخولك</a> للإجابة على هذا السؤال.
        </p>
    @endauth
@endsection
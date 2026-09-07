<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IBBDev</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('questions.index') }}" class="text-xl font-bold text-indigo-600">IBBDev</a>

            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm text-gray-600">
                        {{ auth()->user()->name }} — {{ auth()->user()->reputation_points }} نقطة
                    </span>
                    <a href="{{ route('questions.create') }}" class="text-sm text-indigo-600">سؤال جديد</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-red-500">خروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm">دخول</a>
                    <a href="{{ route('register') }}" class="text-sm">تسجيل</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
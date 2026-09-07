<?php

namespace App\Http\Controllers;

use App\Contracts\QuestionServiceInterface;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Question;

class QuestionController extends Controller
{
    public function __construct(
        protected QuestionServiceInterface $questionService
    ) {}

    public function index()
    {
        $questions = Question::with('user')
            ->withCount('answers')
            ->latest()
            ->paginate(10);

        return view('questions.index', compact('questions'));
    }

    public function show(Question $question)
    {
        $question->load(['user', 'answers.user']);

        return view('questions.show', compact('question'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(StoreQuestionRequest $request)
    {
        $this->questionService->create($request->user(), $request->validated());

        return redirect()->route('questions.index')
            ->with('success', 'تم نشر سؤالك بنجاح.');
    }
}
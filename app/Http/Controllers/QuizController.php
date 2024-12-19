<?php

namespace App\Http\Controllers;

use App\Models\Question;

class QuizController extends Controller
{
    /**
     * Display the quiz page with questions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all questions with their options, eager-loading relationships for better performance
        $questions = Question::with('options')->get();

        // Set the default current question index
        $currentQuestionIndex = 1;

        // Pass questions and the current question index to the view
        return view('quiz', [
            'questions' => $questions,
            'currentQuestionIndex' => $currentQuestionIndex,
        ]);
    }
}

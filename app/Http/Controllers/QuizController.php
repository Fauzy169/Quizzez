<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        // Fetch all the questions from the database
        $questions = Question::with('options')->get();

        // Retrieve answers from session if any
        $answers = session('answers', []);  // Default to an empty array if no answers exist

        // Get current question index from the session or set to 1
        $currentQuestionIndex = session('currentQuestionIndex', 1); // Default to 1 if not set

        return view('quiz', compact('questions', 'currentQuestionIndex', 'answers'));
    }

    // Store answer in session
    public function storeAnswer(Request $request, $questionIndex)
    {
        // Validate the answer
        $request->validate([
            'answer' => 'required|exists:options,id',
        ]);

        // Retrieve the current answers stored in session
        $answers = session('answers', []);

        // Store the selected answer
        $answers[$questionIndex] = $request->answer;

        // Save the updated answers back to the session
        session(['answers' => $answers]);

        // Update the current question index to the next one
        $currentQuestionIndex = $questionIndex + 1;
        session(['currentQuestionIndex' => $currentQuestionIndex]);

        // Get the next question data if available
        $nextQuestion = Question::with('options')->find($currentQuestionIndex);

        return response()->json([
            'success' => true,
            'message' => 'Answer saved successfully',
            'nextQuestion' => $nextQuestion,
            'currentQuestionIndex' => $currentQuestionIndex,
        ]);
    }
}

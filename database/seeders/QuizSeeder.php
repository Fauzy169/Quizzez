<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $questions = [
            [
                'question' => 'Who is making the Web standards?',
                'options' => [
                    ['option_text' => 'The World Wide Web Consortium', 'is_correct' => true],
                    ['option_text' => 'Mozilla', 'is_correct' => false],
                    ['option_text' => 'Microsoft', 'is_correct' => false],
                    ['option_text' => 'Google', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does HTML stand for?',
                'options' => [
                    ['option_text' => 'Hyper Text Markup Language', 'is_correct' => true],
                    ['option_text' => 'High Text Markup Language', 'is_correct' => false],
                    ['option_text' => 'Hyperlinks and Text Markup Language', 'is_correct' => false],
                    ['option_text' => 'Home Tool Markup Language', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does CSS stand for?',
                'options' => [
                    ['option_text' => 'Cascading Style Sheets', 'is_correct' => true],
                    ['option_text' => 'Creative Style Sheets', 'is_correct' => false],
                    ['option_text' => 'Colorful Style Sheets', 'is_correct' => false],
                    ['option_text' => 'Computer Style Sheets', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Which HTML element is used to define a JavaScript script?',
                'options' => [
                    ['option_text' => '<script>', 'is_correct' => true],
                    ['option_text' => '<javascript>', 'is_correct' => false],
                    ['option_text' => '<js>', 'is_correct' => false],
                    ['option_text' => '<code>', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Inside which HTML element do we put the CSS link?',
                'options' => [
                    ['option_text' => '<head>', 'is_correct' => true],
                    ['option_text' => '<body>', 'is_correct' => false],
                    ['option_text' => '<css>', 'is_correct' => false],
                    ['option_text' => '<style>', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $data) {
            $question = Question::create([
                'question' => $data['question'],
            ]);

            $question->options()->createMany($data['options']);
        }
    }
}

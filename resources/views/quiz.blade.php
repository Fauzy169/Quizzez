<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question-map-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
            gap: 10px;
        }

        .question-map-btn.active {
            background-color: #0d6efd;
            color: white;
        }

        .answer-btn.active {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
<div class="container my-4">
    <div class="row">
        <!-- Main Question Area -->
        <div class="col-md-8">
            <div class="card p-3 shadow-sm mb-4">
                <h6>Question: <span id="current-question-index">1</span>/{{ count($questions) }}</h6>
                <p id="question-text">{{ $questions[0]->question }}</p>
                <div id="options-container">
                    @foreach($questions[0]->options as $option)
                        <button class="btn btn-outline-secondary w-100 mb-3 answer-btn" 
                                onclick="selectAnswer(this)">{{ $option->option_text }}</button>
                    @endforeach
                </div>
                <div class="d-flex gap-3 mt-3">
                    <button id="prev-btn" class="btn btn-primary px-4 py-2" onclick="changeQuestion('prev')" disabled>Previous</button>
                    <button class="btn btn-warning px-4 py-2">Ragu-Ragu</button>
                    <button id="next-btn" class="btn btn-success px-4 py-2" onclick="changeQuestion('next')">Next</button>
                </div>
            </div>
        </div>

        <!-- Question Map Area -->
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h6>Question Map:</h6>
                <div class="question-map-container">
                    @foreach($questions as $index => $question)
                        <button class="btn btn-secondary question-map-btn" 
                                id="map-btn-{{ $index + 1 }}" 
                                onclick="goToQuestion({{ $index + 1 }})">{{ $index + 1 }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let questions = @json($questions); // Fetch questions from backend
    let currentQuestionIndex = 1; // Start with question 1

    // Update the question and options on the page
    function updateQuestion() {
        // Update question text
        document.getElementById('question-text').textContent = questions[currentQuestionIndex - 1].question;

        // Update options
        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = ''; // Clear previous options
        questions[currentQuestionIndex - 1].options.forEach(option => {
            const button = document.createElement('button');
            button.className = 'btn btn-outline-secondary w-100 mb-3 answer-btn';
            button.textContent = option.option_text;
            button.onclick = () => selectAnswer(button);
            optionsContainer.appendChild(button);
        });

        // Update current question number
        document.getElementById('current-question-index').textContent = currentQuestionIndex;

        // Update active state in Question Map
        document.querySelectorAll('.question-map-btn').forEach((btn, index) => {
            btn.classList.toggle('active', index + 1 === currentQuestionIndex);
        });

        // Enable/disable Next and Previous buttons
        document.getElementById('prev-btn').disabled = currentQuestionIndex === 1;
        document.getElementById('next-btn').disabled = currentQuestionIndex === questions.length;
    }

    // Handle Next and Previous button clicks
    function changeQuestion(direction) {
        if (direction === 'next' && currentQuestionIndex < questions.length) {
            currentQuestionIndex++;
        } else if (direction === 'prev' && currentQuestionIndex > 1) {
            currentQuestionIndex--;
        }
        updateQuestion();
    }

    // Navigate to a specific question from Question Map
    function goToQuestion(index) {
        currentQuestionIndex = index;
        updateQuestion();
    }

    // Mark the selected answer
    function selectAnswer(button) {
        document.querySelectorAll('.answer-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    }

    // Initialize the first question as active on page load
    document.addEventListener('DOMContentLoaded', updateQuestion);
</script>
</body>
</html>

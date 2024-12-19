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

        .answered {
            background-color: #28a745;
            color: white;
        }

        .question-map-btn.ragu-ragu {
            background-color: #ffc107; /* Warna kuning untuk ragu-ragu */
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
                    <button id="ragu-btn" class="btn btn-warning px-4 py-2" onclick="markAsRagu()">Ragu-Ragu</button>
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
    // Array of questions fetched from the backend
    let questions = @json($questions); 
    let currentQuestionIndex = 1; // Start with question 1
    let answers = Array(questions.length).fill(null); // Initialize an array to store answers (null means no answer selected)
    let raguQuestions = Array(questions.length).fill(false); // Array to track ragu-ragu status

    // Function to update question and its options
    function updateQuestion() {
        const questionData = questions[currentQuestionIndex - 1]; // Get current question data
        
        // Update question text
        document.getElementById('question-text').textContent = questionData.question;

        // Update options
        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = ''; // Clear previous options

        if (questionData.options && questionData.options.length > 0) {
            questionData.options.forEach((option, index) => {
                const button = document.createElement('button');
                button.className = 'btn btn-outline-secondary w-100 mb-3 answer-btn';
                button.textContent = option.option_text; // Display option text
                button.onclick = () => selectAnswer(button, index); // Add click event with option index
                optionsContainer.appendChild(button); // Append to container
                
                // Mark the selected option if it was previously answered
                if (answers[currentQuestionIndex - 1] === index) {
                    button.classList.add('active'); // Highlight selected option
                }
            });
        } else {
            optionsContainer.innerHTML = '<p class="text-muted">No options available for this question.</p>';
        }

        // Update current question index
        document.getElementById('current-question-index').textContent = currentQuestionIndex;

        // Update Question Map active state
        document.querySelectorAll('.question-map-btn').forEach((btn, index) => {
            btn.classList.toggle('active', index + 1 === currentQuestionIndex);
            btn.classList.toggle('answered', answers[index] !== null);
            btn.classList.toggle('ragu-ragu', raguQuestions[index]); // Add ragu-ragu status
        });

        // Enable/Disable Next and Previous buttons
        document.getElementById('prev-btn').disabled = currentQuestionIndex === 1;
        document.getElementById('next-btn').disabled = currentQuestionIndex === questions.length;
    }

    // Function to handle Next and Previous buttons
    function changeQuestion(direction) {
        if (direction === 'next' && currentQuestionIndex < questions.length) {
            currentQuestionIndex++;
        } else if (direction === 'prev' && currentQuestionIndex > 1) {
            currentQuestionIndex--;
        }
        updateQuestion(); // Update question after changing index
    }

    // Function to navigate to a specific question from the Question Map
    function goToQuestion(index) {
        currentQuestionIndex = index;
        updateQuestion(); // Update question after navigating
    }

    // Function to handle answer selection
    function selectAnswer(button, optionId) {
        document.querySelectorAll('.answer-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active'); // Mark the clicked button as active

        const questionIndex = currentQuestionIndex;

        // Save answer locally
        answers[questionIndex - 1] = optionId;

        // Send answer to the server
        fetch(`/quiz/${questionIndex}/answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ answer: optionId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.nextQuestion) {
                        questions[data.currentQuestionIndex - 1] = data.nextQuestion; // Update question data
                        currentQuestionIndex = data.currentQuestionIndex;
                        updateQuestion(); // Show the next question
                    }
                } else {
                    alert('Failed to save the answer.');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Function to mark question as "Ragu-Ragu"
    function markAsRagu() {
        const mapButton = document.getElementById(`map-btn-${currentQuestionIndex}`);

        // Toggle ragu-ragu status
        raguQuestions[currentQuestionIndex - 1] = !raguQuestions[currentQuestionIndex - 1];

        // Update Question Map button
        if (raguQuestions[currentQuestionIndex - 1]) {
            mapButton.classList.add('ragu-ragu');
        } else {
            mapButton.classList.remove('ragu-ragu');
        }
    }

    // Initialize the first question on page load
    document.addEventListener('DOMContentLoaded', updateQuestion);
</script>

</body>
</html>

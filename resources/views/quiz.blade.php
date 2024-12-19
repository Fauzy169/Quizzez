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
            background-color: #0d6efd; /* Blue color for the active question */
            color: white;
        }

        .answer-btn.active {
            background-color: #0d6efd;
            color: white;
        }

        .answered {
            background-color: #28a745; /* Green for confirmed answer */
            color: white;
        }

        .ragu-ragu {
            background-color: #ffc107; /* Yellow for unsure (ragu-ragu) */
            color: white;
        }

        .ragu-ragu-btn {
    position: relative;
    padding-left: 30px; /* Beri ruang di sebelah kiri untuk kotak centang */
}

        .ragu-ragu-btn .check-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            font-size: 18px;
            color: white;
            visibility: hidden; /* Hide the check icon initially */
        }

        .ragu-ragu-btn .check-box {
            width: 20px;
            height: 20px;
            background-color: white;
            border: 2px solid #ffc107; /* Yellow border for the box */
            border-radius: 3px;
            margin-right: 10px;
        }

        .ragu-ragu-btn.checked .check-icon {
            visibility: visible; /* Show the check icon when checked */
        }

        .question-map-btn:not(.answered):not(.ragu-ragu):not(.active) {
            background-color: #6c757d;
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
                                onclick="selectAnswer(this, {{ $loop->index }})">{{ $option->option_text }}</button>
                    @endforeach
                </div>
                <div class="d-flex gap-3 mt-3">
                    <button id="prev-btn" class="btn btn-primary px-4 py-2" onclick="changeQuestion('prev')" disabled>Previous</button>
                    <button id="ragu-ragu-btn" class="btn btn-warning px-4 py-2 ragu-ragu-btn" onclick="markRaguRagu()">
                        <span class="check-box"></span> Ragu-Ragu
                        <span class="check-icon">&#10004;</span>
                    </button>
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
    let raguRagu = Array(questions.length).fill(false); // Track which questions are marked as ragu-ragu

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
                
                // Highlight selected option if it is the current answer
                if (answers[currentQuestionIndex - 1] === index) {
                    button.classList.add('answered'); // Highlight selected option in green
                }

                // If this question is marked as ragu-ragu, highlight the selected option only
                if (raguRagu[currentQuestionIndex - 1] && answers[currentQuestionIndex - 1] === index) {
                    button.classList.add('ragu-ragu');
                }
            });
        } else {
            optionsContainer.innerHTML = '<p class="text-muted">No options available for this question.</p>';
        }

        // Update current question index
        document.getElementById('current-question-index').textContent = currentQuestionIndex;

        // Update Question Map active state
        document.querySelectorAll('.question-map-btn').forEach((btn, index) => {
            // Mark as active if current question is selected
            btn.classList.toggle('active', index + 1 === currentQuestionIndex);
            // Highlight answered questions in green
            if (answers[index] !== null) {
                btn.classList.add('answered');
            } else {
                btn.classList.remove('answered');
            }
            // Highlight ragu-ragu questions in yellow
            if (raguRagu[index] && answers[index] !== null) {
                btn.classList.add('ragu-ragu');
            } else {
                btn.classList.remove('ragu-ragu');
            }
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
        // Update question after changing index
        updateQuestion();
    }

    // Function to navigate to a specific question from the Question Map
    function goToQuestion(index) {
        currentQuestionIndex = index;
        updateQuestion(); // Update question after navigating
    }

    // Function to handle answer selection and toggling
    function selectAnswer(button, optionId) {
        const questionIndex = currentQuestionIndex - 1; // Get the current question index

        // If the same button is clicked again, deselect the answer
        if (answers[questionIndex] === optionId) {
            button.classList.remove('answered'); // Remove green color from the previously selected option
            answers[questionIndex] = null; // Clear the answer
        } else {
            // Mark the new selected button as active
            document.querySelectorAll('.answer-btn').forEach(btn => btn.classList.remove('answered')); // Remove green from all options
            button.classList.add('answered'); // Add green to the selected button
            answers[questionIndex] = optionId; // Store the selected answer
        }

        // Update the question map button color
        updateQuestion();
    }

    // Function to mark a question as "ragu-ragu" (uncertain)
    function markRaguRagu() {
        const questionIndex = currentQuestionIndex - 1;
        
        // Toggle the ragu-ragu state
        raguRagu[questionIndex] = !raguRagu[questionIndex];
        
        // Toggle the check mark in the Ragu-Ragu button
        const raguBtn = document.getElementById('ragu-ragu-btn');
        raguBtn.classList.toggle('checked', raguRagu[questionIndex]);

        // Apply color changes based on the new ragu-ragu state
        updateQuestion(); // Update question map and question state
    }

    // Initial question load
    updateQuestion();
</script>
</body>
</html>

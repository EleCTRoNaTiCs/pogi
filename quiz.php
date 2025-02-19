<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$questions = $conn->query("SELECT * FROM quiz_questions");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = 0;
    $total_questions = $questions->num_rows;
    foreach ($questions as $question) {
        $q_id = $question['id'];
        if (isset($_POST["q$q_id"]) && $_POST["q$q_id"] == $question['correct_option']) {
            $score++;
        }
    }

    $conn->query("INSERT INTO user_progress (user_id, score, attempts) VALUES ('$user_id', '$score', 1) 
                  ON DUPLICATE KEY UPDATE score='$score', attempts=attempts+1, last_attempt=NOW()");

    // Thematic feedback based on score
    if ($score == $total_questions) {
        $message = "
            <style>
                .feedback-container {
                    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
                    padding: 2rem;
                    border-radius: 1rem;
                    color: white;
                    text-align: center;
                    animation: fadeIn 0.5s ease-out;
                    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
                }
                
                .feedback-container h2 {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                    background: linear-gradient(45deg, #fff, #f0f9ff);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                
                .feedback-container p {
                    font-size: 1.1rem;
                    line-height: 1.6;
                    margin-bottom: 1.5rem;
                }
                
                .emoji {
                    font-size: 2.5rem;
                    margin: 0 0.5rem;
                    animation: bounce 1s infinite;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-10px); }
                }
                
                .score-highlight {
                    background: rgba(255, 255, 255, 0.2);
                    padding: 0.5rem 1rem;
                    border-radius: 2rem;
                    display: inline-block;
                    margin: 1rem 0;
                    backdrop-filter: blur(4px);
                }
            </style>
            <div class='feedback-container'>
                <h2><span class='emoji'>🌟</span> Virtual Reality Master! <span class='emoji'>🌟</span></h2>
                <div class='score-highlight'>Score: $score/$total_questions</div>
                <p>You have demonstrated exceptional understanding of emotional and mental wellness in the digital age! Keep leading the way in innovative counseling! 🚀</p>
            </div>";
    } elseif ($score >= $total_questions * 0.8) {
        $message = "
            <style>
                .feedback-container {
                    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                    padding: 2rem;
                    border-radius: 1rem;
                    color: white;
                    text-align: center;
                    animation: fadeIn 0.5s ease-out;
                    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
                }
                
                .feedback-container h2 {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                    background: linear-gradient(45deg, #fff, #f0f9ff);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                
                .feedback-container p {
                    font-size: 1.1rem;
                    line-height: 1.6;
                    margin-bottom: 1.5rem;
                }
                
                .emoji {
                    font-size: 2.5rem;
                    margin: 0 0.5rem;
                    animation: pulse 1.5s infinite;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
                
                .score-highlight {
                    background: rgba(255, 255, 255, 0.2);
                    padding: 0.5rem 1rem;
                    border-radius: 2rem;
                    display: inline-block;
                    margin: 1rem 0;
                    backdrop-filter: blur(4px);
                }
            </style>
            <div class='feedback-container'>
                <h2><span class='emoji'>🎉</span> Almost There! <span class='emoji'>🎉</span></h2>
                <div class='score-highlight'>Score: $score/$total_questions</div>
                <p>Your grasp on leveraging VR for wellness is impressive! With just a bit more practice, you'll be pioneering the future of counseling! 🌍💡</p>
            </div>";
    } elseif ($score >= $total_questions * 0.5) {
        $message = "
            <style>
                .feedback-container {
                    background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
                    padding: 2rem;
                    border-radius: 1rem;
                    color: white;
                    text-align: center;
                    animation: fadeIn 0.5s ease-out;
                    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
                }
                
                .feedback-container h2 {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                    background: linear-gradient(45deg, #fff, #f0f9ff);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                
                .feedback-container p {
                    font-size: 1.1rem;
                    line-height: 1.6;
                    margin-bottom: 1.5rem;
                }
                
                .emoji {
                    font-size: 2.5rem;
                    margin: 0 0.5rem;
                    animation: wave 2s infinite;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                
                @keyframes wave {
                    0%, 100% { transform: rotate(0deg); }
                    50% { transform: rotate(15deg); }
                }
                
                .score-highlight {
                    background: rgba(255, 255, 255, 0.2);
                    padding: 0.5rem 1rem;
                    border-radius: 2rem;
                    display: inline-block;
                    margin: 1rem 0;
                    backdrop-filter: blur(4px);
                }
            </style>
            <div class='feedback-container'>
                <h2><span class='emoji'>😊</span> Keep Growing! <span class='emoji'>😊</span></h2>
                <div class='score-highlight'>Score: $score/$total_questions</div>
                <p>You're on the right track to integrating technology into emotional well-being. Keep exploring new ways to enhance guidance counseling! 💙</p>
            </div>";
    } else {
        $message = "
            <style>
                .feedback-container {
                    background: linear-gradient(135deg, #6a11cb 0%, #ef4444 100%);
                    padding: 2rem;
                    border-radius: 1rem;
                    color: white;
                    text-align: center;
                    animation: fadeIn 0.5s ease-out;
                    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.2);
                }
                
                .feedback-container h2 {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                    background: linear-gradient(45deg, #fff, #f0f9ff);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                
                .feedback-container p {
                    font-size: 1.1rem;
                    line-height: 1.6;
                    margin-bottom: 1.5rem;
                }
                
                .emoji {
                    font-size: 2.5rem;
                    margin: 0 0.5rem;
                    animation: shine 2s infinite;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                
                @keyframes shine {
                    0% { opacity: 1; }
                    50% { opacity: 0.5; }
                    100% { opacity: 1; }
                }
                
                .score-highlight {
                    background: rgba(255, 255, 255, 0.2);
                    padding: 0.5rem 1rem;
                    border-radius: 2rem;
                    display: inline-block;
                    margin: 1rem 0;
                    backdrop-filter: blur(4px);
                }
            </style>
            <div class='feedback-container'>
                <h2><span class='emoji'>💡</span> A Journey Begins! <span class='emoji'>💡</span></h2>
                <div class='score-highlight'>Score: $score/$total_questions</div>
                <p>Every great counselor starts with a single step! Keep learning and soon you'll be transforming mental wellness through VR! 🚀✨</p>
            </div>";
    }
    
    echo "<center><div class='feedback'>$message<br><button style='background-color:rgb(148, 238, 189); border: none; padding: 5px; border-radius: 10px; cursor: pointer;'><a href='progress.php' class='btn-view-progress' style='text-decoration: none; color: black; font-size: 18px;'>View Progress</a></button></div><center>";
    exit(); // Prevents form resubmission on page refresh
}

//Fetch Username
$query_username = "SELECT username FROM users WHERE role = 'User'";
$result_username = $conn->query($query_username);
$username = $result_username->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Quiz - Beyond the Session</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: url("Background/bg.jpg") no-repeat center/cover fixed;
            font-family: 'Roboto', sans-serif;
            color: #fff;
            padding: 40px 0;
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            max-width: 900px;
            width: 100%;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        /* Question styling */
        .question {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Custom Radio Button Styles */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .radio-button {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 18px;
        }

        .radio-button input {
            margin-right: 10px;
        }

        .radio-label {
            padding: 12px 20px;
            border: 2px solid #6a11cb;
            border-radius: 25px;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        .radio-button input:checked + .radio-label {
            background-color: #6a11cb;
            color: #fff;
        }

        .btn-submit {
            padding: 14px 30px;
            font-size: 20px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #2575fc;
        }

        .feedback {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-view-progress {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background: #6a11cb;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-view-progress:hover {
            background: #2575fc;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" style="background: url('Background/bg.jpg') no-repeat center/cover fixed;">
        <div class="container-fluid">
            <div class="d-flex flex-column">
                <span class="badge bg-info text-white px-3 mb-1">WELCOME TO</span>
                    <h4 class="text-white mb-0 fw-bolder text-uppercase border-bottom border-info pb-1">VR EMOTIONAL WELL-BEING</h4>
                        <span class="badge bg-light text-primary mt-1">
                            <?php echo strtoupper($username['username']); ?>
                            <i class="bi bi-person-check-fill ms-2"></i>
                        </span>
                    </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="index.php" class="nav-link fw-bold text-light d-flex align-items-center">Home</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="progress.php" class="nav-link fw-bold text-light d-flex align-items-center">View Progress</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="VR/index.php" class="nav-link fw-bold text-light d-flex align-items-center">Start VR</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="quiz.php" class="nav-link fw-bold text-light d-flex align-items-center">Take Quiz</a></li>
                    <li class="nav-link fw-bold text-light d-flex align-items-center gap-2 position-relative btn btn-outline-info shadow-sm px-3 py-2"><a href="logout.php" class="nav-link fw-bold text-light d-flex align-items-center">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav><br><br><hr style="color: white;"><br>

<div class="container">
    <h1>Quiz - Beyond the Session</h1>
    <form method="POST">
        <?php foreach ($questions as $question): ?>
            <div class="question">📝 <?php echo $question['question']; ?></div>
            <div class="radio-group">
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="A" required>
                    <span class="radio-label"> <?php echo $question['option_a']; ?></span>
                </label>
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="B" required>
                    <span class="radio-label"> <?php echo $question['option_b']; ?></span>
                </label>
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="C" required>
                    <span class="radio-label"> <?php echo $question['option_c']; ?></span>
                </label>
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="D" required>
                    <span class="radio-label"> <?php echo $question['option_d']; ?></span>
                </label>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-submit">🚀 Submit</button>
    </form>
</div>

</body>
</html>

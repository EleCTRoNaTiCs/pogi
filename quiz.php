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
    foreach ($questions as $question) {
        $q_id = $question['id'];
        if (isset($_POST["q$q_id"]) && $_POST["q$q_id"] == $question['correct_option']) {
            $score++;
        }
    }

    $conn->query("INSERT INTO user_progress (user_id, score, attempts) VALUES ('$user_id', '$score', 1) 
                  ON DUPLICATE KEY UPDATE score='$score', attempts=attempts+1, last_attempt=NOW()");

    echo "You scored $score/10! <a href='progress.php'>View Progress</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - Beyond the Session</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f8fa;
            color: #333;
            padding: 40px 0;
        }

        h1 {
            color: #6a11cb;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            max-width: 900px;
            width: 100%;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* Question styling */
        .question {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }

        /* Custom Radio Button Styles */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .radio-button {
            position: relative;
            display: flex;
            align-items: center;
            padding-left: 35px;
            cursor: pointer;
            font-size: 16px;
            color: #333;
        }

        .radio-button input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .radio-button input:checked + .radio-label {
            background-color:  #6a11cb;
            color: #fff;
            border: 2px solid #6a11cb;
        }

        .radio-label {
            display: inline-block;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .radio-button input:focus + .radio-label {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.6);
        }

        .radio-button input:checked + .radio-label:before {
            content: '\2022';
            font-size: 24px;
            color: #fff;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .radio-button input:disabled + .radio-label {
            background-color: #e0e0e0;
            color: #b0b0b0;
            cursor: not-allowed;
        }

        .btn-submit {
            padding: 12px 25px;
            font-size: 18px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #2575fc;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Quiz - Beyond the Session</h1>
    <form method="POST">
        <?php foreach ($questions as $question): ?>
            <div class="question"><?php echo $question['question']; ?></div>
            <div class="radio-group">
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="A">
                    <span class="radio-label"><?php echo $question['option_a']; ?></span>
                </label>
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="B">
                    <span class="radio-label"><?php echo $question['option_b']; ?></span>
                </label>
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="C">
                    <span class="radio-label"><?php echo $question['option_c']; ?></span>
                </label>
                <label class="radio-button">
                    <input type="radio" name="q<?php echo $question['id']; ?>" value="D">
                    <span class="radio-label"><?php echo $question['option_d']; ?></span>
                </label>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-submit">Submit</button>
    </form>
</div>

</body>
</html>

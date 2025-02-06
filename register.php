<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Beyond the Session</title>
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
            max-width: 500px;
            width: 100%;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .input-field {
            width: 100%;
            padding: 12px 20px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .input-field:focus {
            border-color: #6a11cb;
            outline: none;
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
            width: 100%;
        }

        button:hover {
            background-color: #2575fc;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer a {
            color: #6a11cb;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
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
    <h1>Registration</h1>
    <form method="POST">
        <input type="text" name="username" class="input-field" placeholder="Username" required>
        <input type="password" name="password" class="input-field" placeholder="Password" required>
        <button type="submit" class="btn-submit">Register</button>
    </form>
    <div class="form-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>

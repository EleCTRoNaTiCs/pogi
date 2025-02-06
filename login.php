<?php
include 'config.php';
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // or any page the user should land after login
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Protect against SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify the password securely
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; // Store user ID in session
            header("Location: index.php"); // Redirect to dashboard
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            font-size: 28px;
            color: #6a11cb;
            margin-bottom: 30px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #6a11cb;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #6a11cb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2575fc;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                padding: 30px;
                width: 90%;
            }

            h2 {
                font-size: 24px;
            }

            input[type="text"], input[type="password"], button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Welcome Back!</h2>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button><br><hr>
            <button style="width: 100px; padding: 9px;"><a href="register.php" style="text-decoration: none; color: white;">Register</a></button>
        </form>

        <!-- Error Message -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $result->num_rows == 0) {
            echo '<p class="error-message">User not found.</p>';
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !password_verify($password, $row['password'])) {
            echo '<p class="error-message">Incorrect password.</p>';
        }
        ?>
    </div>

</body>

</html>

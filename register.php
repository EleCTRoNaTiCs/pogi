
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Beyond the Session</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url("Background/bg.jpg") no-repeat center/cover fixed;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

<div class="register-container p-4">
    
    <?php
        include 'config.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
            
            if ($conn->query($sql) === TRUE) {
                echo '<div class="alert alert-success text-center p-3 rounded">🎉 Registration Successful! <br> <a href="login.php" class="btn btn-primary mt-2">Go to Login</a></div>';
            } else{
                echo '<div class="alert alert-danger text-center p-3 rounded">❌ Registration Failed! <br> Error: ' . $conn->error . ' <br> <a href="register.php" class="btn btn-danger mt-2">Try Again</a></div>';
            } 
        }
        
    ?>

    <h1 class="text-primary text-center fw-bold mb-4">Registration</h1>
    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control bg-transparent border p-2" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control bg-transparent border p-2" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <div class="text-center mt-3">
        <p>Already have an account? <a href="login.php" class="text-primary fw-bold">Login here</a></p>
    </div>
</div>

</body>
</html>


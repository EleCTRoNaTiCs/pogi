<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .login-container {
            background-color: rgba(255, 255, 255, 0.5);
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
    </style>
</head>

<body>
    <div class="login-container p-4">
     
    <?php
    include 'config.php';
    session_start();

    // Redirect to dashboard if already logged in
    if (isset($_SESSION['user_id']) && isset($_SESSION['Role'])) {
        if ($_SESSION['Role'] == 'Admin') {
            header("Location: Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
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
                $_SESSION['Role'] = $row['role']; // Store role in session

                // Redirect based on role
                if ($row['Role'] == 'Admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                echo '<div class="alert alert-danger text-center p-3 rounded shadow-lg" style="max-width: 400px; margin: auto;">
                        <h5 class="fw-bold">❌ Incorrect Password</h5>
                        <p>Please try again.</p>
                      </div>';
            }
        } else {
            echo '<div class="alert alert-warning text-center p-3 rounded shadow-lg" style="max-width: 400px; margin: auto;">
                    <h5 class="fw-bold">⚠️ User Not Found</h5>
                    <p>Check your username or <a href="register.php" class="text-decoration-none fw-bold text-primary">register here</a>.</p>
                  </div>';
        }
    }
?><br><br>


        <h2 class="text-primary fw-bold mb-4">Welcome Back!</h2>
        
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control bg-transparent border p-2" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control bg-transparent border p-2" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <hr>
            <a href="register.php" class="btn btn-secondary w-100">Register</a>
        </form>


    </div>

</body>

</html>


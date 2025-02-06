<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user progress (score, attempts, last attempt)
$query = "SELECT score, attempts, last_attempt FROM user_progress WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_progress = $result->fetch_assoc();
$stmt->close();

// If no progress record, initialize with zero
if (!$user_progress) {
    $user_progress = ['score' => 0, 'attempts' => 0, 'last_attempt' => 'No attempts yet'];
}

// Fetch quiz history (previous attempts)
$query_history = "SELECT score, attempts, last_attempt FROM user_progress WHERE user_id = ? ORDER BY last_attempt DESC";
$stmt_history = $conn->prepare($query_history);
$stmt_history->bind_param("i", $user_id);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
$stmt_history->close();

// Dummy emotional stats data (replace with actual DB data later)
$emotions = ['Stress', 'Happiness', 'Anxiety', 'Calmness'];
$emotion_data = [rand(10, 40), rand(20, 50), rand(5, 30), rand(30, 60)];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VR Emotional Well-being Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f1f3f6;
            transition: padding-left 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background: #6a11cb;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar h2 {
            margin: 0;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
            transition: background-color 0.3s;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
            border-radius: 8px;
            display: block;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar ul li.active a {
            background-color: #2575fc;
        }

        /* Dashboard */
        .dashboard {
            padding: 30px;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        .dashboard h1 {
            text-align: center;
            font-size: 36px;
            color: #333;
            margin-bottom: 30px;
        }

        /* Stats Section */
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .stat-box {
            background-color: #fff;
            border-radius: 8px;
            padding: 25px;
            width: 30%;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
        }

        .stat-box h3 {
            font-size: 24px;
            color: #6a11cb;
            margin-bottom: 10px;
        }

        .stat-box p {
            font-size: 20px;
            color: #333;
        }

        /* Progress Table */
        .progress-list {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .progress-list h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .progress-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .progress-list th, .progress-list td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            color: #333;
        }

        .progress-list th {
            background-color: #6a11cb;
            color: white;
        }

        .progress-list tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .progress-list tr:hover {
            background-color: #ddd;
        }

        /* Chart Section */
        .charts {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .chart-box {
            width: 48%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .dashboard {
                margin-left: 0;
                padding: 20px;
            }

            .stat-box {
                width: 48%;
                margin-bottom: 20px;
            }

            .charts {
                flex-direction: column;
            }

            .chart-box {
                width: 100%;
                margin-bottom: 20px;
            }

            .hamburger {
                display: flex;
                cursor: pointer;
                z-index: 999;
                padding: 10px;
                position: fixed;
                top: 20px;
                left: 20px;
                background-color: rgba(0, 0, 0, 0.5);
                border-radius: 5px;
            }

            .hamburger div {
                width: 30px;
                height: 4px;
                background-color: white;
                margin: 5px 0;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 100%;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>VR Emotional Well-being</h2>
        <ul>
            <li class="nav-item active"><a href="progress.php">View Progress</a></li>
            <li class="nav-item"><a href="quiz.php">Start Quiz</a></li>
            <li class="nav-item"><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Dashboard -->
    <div class="dashboard">
        <h1>Your Progress</h1>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-box">
                <h3>Score</h3>
                <p><?= $user_progress['score'] ?> / 100</p>
            </div>
            <div class="stat-box">
                <h3>Attempts</h3>
                <p><?= $user_progress['attempts'] ?></p>
            </div>
            <div class="stat-box">
                <h3>Last Attempt</h3>
                <p><?= $user_progress['last_attempt'] ?></p>
            </div>
        </div>

        <!-- Progress Table -->
        <div class="progress-list">
            <h3>Your Quiz History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Attempt Date</th>
                        <th>Score</th>
                        <th>Attempts</th>
                        <th>Last Attempt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($history = $result_history->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $history['last_attempt'] ?></td>
                            <td><?= $history['score'] ?></td>
                            <td><?= $history['attempts'] ?></td>
                            <td><?= $history['last_attempt'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Charts Section -->
        <div class="charts">
            <!-- Progress Bar Chart -->
            <div class="chart-box">
                <canvas id="progress-chart"></canvas>
            </div>

            <!-- Emotional Stats Pie Chart -->
            <div class="chart-box">
                <canvas id="emotion-chart"></canvas>
            </div>
        </div>

    </div>

    <!-- Hamburger Menu for Mobile -->
    <div class="hamburger" onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Chart.js - Progress Bar Chart
        const ctxProgress = document.getElementById('progress-chart').getContext('2d');
        const progressChart = new Chart(ctxProgress, {
            type: 'bar',
            data: {
                labels: ['Progress'],
                datasets: [{
                    label: 'Emotional Progress (%)',
                    data: [<?= $user_progress['score'] ?>],
                    backgroundColor: '#6a11cb',
                    borderColor: '#2575fc',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Chart.js - Emotional Stats Pie Chart
        const ctxEmotion = document.getElementById('emotion-chart').getContext('2d');
        const emotionChart = new Chart(ctxEmotion, {
            type: 'pie',
            data: {
                labels: ['Stress', 'Happiness', 'Anxiety', 'Calmness'],
                datasets: [{
                    data: [<?= implode(',', $emotion_data) ?>],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            }
        });
    </script>

</body>

</html>

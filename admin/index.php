<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch quiz statistics
$query_stats = "SELECT 
                    COALESCE(AVG(score), 0) AS avg_score, 
                    COALESCE(SUM(attempts), 0) AS total_attempts, 
                    MAX(last_attempt) AS latest_attempt 
                FROM user_progress";
$result_stats = $conn->query($query_stats);
$stats = $result_stats->fetch_assoc();

//Fetch Username
$query_username = "SELECT username FROM users WHERE role = 'User'";
$result_username = $conn->query($query_username);
$username = $result_username->fetch_assoc();

// Fetch all users' quiz history
$query_history = "SELECT user_id, score, attempts, last_attempt FROM user_progress ORDER BY last_attempt DESC";
$result_history = $conn->query($query_history);

// Dummy emotional stats (replace with actual DB data later)
$emotions = ['Stress', 'Happiness', 'Anxiety', 'Calmness'];
$emotion_data = [rand(10, 40), rand(20, 50), rand(5, 30), rand(30, 60)];


// Fetch score progress over time
$query_progress = "SELECT score, last_attempt 
                  FROM user_progress 
                  ORDER BY last_attempt ASC";
$result_progress = $conn->query($query_progress);

// Initialize arrays for the line chart
$dates = [];
$scores = [];

if ($result_progress->num_rows > 0) {
    while($row = $result_progress->fetch_assoc()) {
        $dates[] = date("d M", strtotime($row['last_attempt']));
        $scores[] = $row['score'];
    }
}

// Convert to JSON for JavaScript
$dates_json = json_encode($dates);
$scores_json = json_encode($scores);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Progress</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body 
            { 
            background: url("../Background/bg.jpg") no-repeat center/cover fixed;
            overflow-x: hidden; 
        }
        .sidebar { width: 250px; }
        @media (max-width: 768px) {
            .sidebar { position: fixed; width: 100%; height: auto; z-index: 1000; }
            .content { margin-top: 80px; }
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" style="background: url('../Background/bg.jpg') no-repeat center/cover fixed;">
        <div class="container-fluid">
         <h4 class="text-white mb-0 fw-bolder text-uppercase border-bottom border-info pb-1">ADMIN DASHBOARD</h4>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link fw-bold text-light">Dashboard</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link fw-bold text-light">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content mt-5 pt-4">
        <h1 class="text-center mt-4 text-white">Users' Quiz Statistics</h1>

        <div class="row mt-4 text-center">
            <div class="col-md-4 col-12 mb-3">
                <div class="card p-3 shadow-sm">
                    <h3 class="text-primary">Average Score</h3>
                    <p class="fs-4"><?= round($stats['avg_score'], 2); ?> / 10</p>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
                <div class="card p-3 shadow-sm">
                    <h3 class="text-primary">Total Attempts</h3>
                    <p class="fs-4"><?= $stats['total_attempts']; ?></p>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
                <div class="card p-3 shadow-sm">
                    <h3 class="text-primary">Last Attempt</h3>
                    <p class="fs-4">
                        <?= !empty($stats['latest_attempt']) ? date("j F Y, g:i A", strtotime($stats['latest_attempt'])) : 'No attempts yet'; ?>
                    </p>
                </div>
            </div>
        </div>


        <!-- User Quiz History Table -->
        <div class="card p-4 shadow-sm">
            <h3 class="text-center">Users' Quiz History</h3>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>Attempt Date</th>
                            <th>Score</th>
                            <th>Attempts</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_history->num_rows > 0) : ?>
                            <?php while ($history = $result_history->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $username['username']?></td>
                                    <td><?= !empty($history['last_attempt']) ? date("j F Y, g:i A", strtotime($history['last_attempt'])) : 'No attempts yet'; ?></td>
                                    <td><?= htmlspecialchars($history['score']); ?></td>
                                    <td><?= htmlspecialchars($history['attempts']); ?></td>
                                    <td>
                                        <form action="delete_quiz_history.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            <input type="hidden" name="user_id" value="<?= $history['user_id']; ?>">
                                            <input type="hidden" name="last_attempt" value="<?= $history['last_attempt']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-muted"><strong>No Quiz History Found!</strong></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-4">
            <!-- Average User Chart -->
            <div class="col-md-6 col-12 mb-3">
                <div class="card p-4 shadow-sm">
                    <canvas id="progress-chart"></canvas>
                </div>
            </div>
      
            <!-- User Quiz Score Progress Chart -->
            <div class="col-md-6 col-12 mb-3">
                <div class="card p-4 shadow-sm">
                    <canvas id="score-progress-chart"></canvas>
                </div>
            </div>

            <!-- Emotion Chart -->
            <div class="col-md-6 col-12 mb-3">
                <div class="card p-4 shadow-sm">
                    <canvas id="emotion-chart"></canvas>
                </div>
            </div>
        </div>

    <script>
         // Average User script //
        const ctxProgress = document.getElementById('progress-chart').getContext('2d');
        const progressChart = new Chart(ctxProgress, {
            type: 'bar',
            data: {
                labels: ['Overall Progress'],
                datasets: [{
                    label: 'Average User Score (%)',
                    data: [<?= round($stats['avg_score'], 2); ?>],
                    backgroundColor: 'lightpink',
                    borderColor: 'lightpink',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10
                    }
                }
            }
        });

         // User Quiz Score Progress script //
        const ctxScoreProgress = document.getElementById('score-progress-chart').getContext('2d');
        const scoreProgressChart = new Chart(ctxScoreProgress, {
            type: 'line',
            data: {
                labels: <?php echo $dates_json; ?>,
                datasets: [{
                    label: 'Quiz Score Progress',
                    data: <?php echo $scores_json; ?>,
                    borderColor: '#2575fc',
                    backgroundColor: 'rgba(37, 117, 252, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6a11cb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'User Quiz Score Progress Over Time',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Emotion Chart //
        const ctxEmotion = document.getElementById('emotion-chart').getContext('2d');
        const emotionChart = new Chart(ctxEmotion, {
            type: 'pie',
            data: {
                labels: ['Stress', 'Happiness', 'Anxiety', 'Calmness'],
                datasets: [{
                    data: [<?= implode(',', $emotion_data) ?>],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            },
            options: {
                responsive: true
            }
        });

    </script>
</body>

</html>

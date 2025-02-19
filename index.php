<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user progress (score, attempts, last attempt)
$query = "SELECT score, attempts, last_attempt FROM user_progress WHERE user_id = ? ORDER BY last_attempt DESC LIMIT 1";
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
$latest_attempt = $result_history->fetch_assoc();
$stmt_history->close();

// Dummy emotional stats data (replace with actual DB data later)
$emotions = ['Stress', 'Happiness', 'Anxiety', 'Calmness'];
$emotion_data = [rand(10, 40), rand(20, 50), rand(5, 30), rand(30, 60)];

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
    <title>VR Emotional Well-being</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url("Background/bg.jpg") no-repeat center/cover fixed;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                width: 100%;
                height: auto;
                z-index: 1000;
            }
            .content {
                margin-top: 80px;
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
    </nav><br><br><br><br><hr style="color: white; ">

    <div class="container content mt-5 pt-4">
        <h1 class="text-center mt-4 text-light">Your Progress</h1>
        <div class="row mt-4 text-center">
            <div class="col-md-4 col-12 mb-3">
                <div class="card p-3 shadow-sm">
                    <h3 class="text-primary">Score</h3>
                    <p class="fs-4"><?php echo $user_progress['score']; ?> / 10</p>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
                <div class="card p-3 shadow-sm">
                    <h3 class="text-primary">Attempts</h3>
                    <p class="fs-4"><?php echo $user_progress['attempts']; ?></p>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-3">
                <div class="card p-3 shadow-sm">
                    <h3 class="text-primary">Last Attempt</h3>
                    <p class="fs-4"><?php echo !empty($latest_attempt['last_attempt']) ? date("j F Y, g:i A", strtotime($latest_attempt['last_attempt'])) : 'No attempts yet'; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center my-4">
            <object id="UnityObject" classid="clsid:444785F1-DE89-4295-863A-D46C3A781394" width="100%" height="500">
                <embed id="UnityEmbed" value="VR/Caps.exe" width="100%" height="500">
            </object>
        </div>

        <div class="card p-4 shadow-sm">
            <h3 class="text-center">Your Quiz History</h3>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Attempt Date</th>
                            <th>Score</th>
                            <th>Attempts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reset the result set pointer before looping
                        $result_history->data_seek(0);

                        if ($result_history->num_rows > 0) : 
                            while ($history = $result_history->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= !empty($history['last_attempt']) ? date("j F Y, g:i A", strtotime($history['last_attempt'])) : 'No attempts yet'; ?></td>
                                    <td><?= htmlspecialchars($history['score']); ?></td>
                                    <td><?= htmlspecialchars($history['attempts']); ?></td>
                                </tr>
                            <?php endwhile; 
                        else : ?>
                            <tr>
                                <td colspan="3" class="text-muted"><strong>No Quiz History Found!</strong></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="row mt-4">
            <div class="col-md-6 col-12 mb-3">
                <div class="card p-4 shadow-sm">
                    <canvas id="progress-chart"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-3">
                <div class="card p-4 shadow-sm">
                    <canvas id="emotion-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // progress script //
        const ctxProgress = document.getElementById('progress-chart').getContext('2d');
        const progressChart = new Chart(ctxProgress, {
            type: 'bar',
            data: {
                labels: ['Progress'],
                datasets: [{
                    label: 'Emotional Progress (%)',
                    data: [<?= $user_progress['score'] ?>],
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

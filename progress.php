<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Deletion
if (isset($_POST['delete_progress_id'])) {
    $progress_id = $_POST['delete_progress_id'];

    // Prepare the SQL to delete the progress record
    $delete_sql = "DELETE FROM user_progress WHERE id = '$progress_id' AND user_id = '$user_id'";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Progress record deleted successfully.'); window.location.href = 'progress.php';</script>";
    } else {
        echo "<script>alert('Error deleting progress record.'); window.location.href = 'progress.php';</script>";
    }
}

$sql = "SELECT * FROM user_progress WHERE user_id = '$user_id' ORDER BY last_attempt DESC";
$result = $conn->query($sql);

// Initialize arrays to hold chart data
$dates = [];
$scores = [];
$attempts = [];

while ($row = $result->fetch_assoc()) {
    // Collect data for charts
    $dates[] = date("F j, Y, g:i a", strtotime($row['last_attempt']));
    $scores[] = $row['score'];
    $attempts[] = $row['attempts'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Progress - Beyond the Session</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            color: #4CAF50;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Container for content */
        .container {
            max-width: 900px;
            width: 100%;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* Button styles */
        .btn {
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-dashboard {
            background-color: #007bff;
            color: white;
        }
        .btn-dashboard:hover {
            background-color: #0056b3;
        }
        .btn-logout {
            background-color: #e74c3c;
            color: white;
        }
        .btn-logout:hover {
            background-color: #c0392b;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }

        /* Table Styling */
        .progress-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .progress-table th, .progress-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .progress-table th {
            background-color: #4CAF50;
            color: white;
        }
        .progress-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .progress-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Charts Section */
        .charts {
            margin-top: 40px;
        }
        .chart-box {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }
            .btn {
                width: 100%;
                display: block;
                margin-bottom: 10px;
            }
            .progress-table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Progress</h1>
    <p>Review your quiz performance below:</p>

    <!-- Progress Table -->
    <table class="progress-table">
        <tr>
            <th>Score</th>
            <th>Attempts</th>
            <th>Last Attempt</th>
            <th>Action</th>
        </tr>
        <?php
        // Display the last progress entry
        $result->data_seek(0); // Reset pointer to fetch again
        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['score'] ?>/10</td>
                <td><?= $row['attempts'] ?></td>
                <td><?= date("F j, Y, g:i a", strtotime($row['last_attempt'])) ?></td>
                <td>
                    <!-- Delete Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_progress_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this progress record?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="index.php" class="btn btn-dashboard">Back to Dashboard</a>
    <a href="quiz.php" class="btn">Retake Quiz</a>
    <a href="logout.php" class="btn btn-logout">Logout</a>
</div>

<!-- Chart Section -->
<div class="charts container">
    <!-- Score Progress Line Chart -->
    <div class="chart-box">
        <canvas id="scoreProgressChart"></canvas>
    </div>

    <!-- Emotional Stats Pie Chart (Dummy Data) -->
    <div class="chart-box">
        <canvas id="emotionStatsChart"></canvas>
    </div>
</div>

<script>
// Score Progress Line Chart (Responsive)
var ctx = document.getElementById('scoreProgressChart').getContext('2d');
var scoreProgressChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Score Progress Over Time',
            data: <?php echo json_encode($scores); ?>,
            fill: false,
            borderColor: '#4CAF50',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// Emotional Stats Pie Chart (Dummy Data)
var ctxEmotion = document.getElementById('emotionStatsChart').getContext('2d');
var emotionStatsChart = new Chart(ctxEmotion, {
    type: 'pie',
    data: {
        labels: ['Stress', 'Happiness', 'Anxiety', 'Calmness'],
        datasets: [{
            data: [<?php echo implode(",", $attempts); ?>], // Real-time attempt data for emotional stats
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>

<?php
include '../config.php';
session_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $capacity = intval($_POST['capacity']);
    $date = $_POST['date'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    $stmt = $conn->prepare("INSERT INTO available_lab (Name, Type, Capacity, Available_Date, Start_Time, End_Time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $name, $type, $capacity, $date, $start, $end);
    $stmt->execute();
    header("Location: labto_labRecord.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Lab</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            flex-shrink: 0;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            color: #ecf0f1;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #34495e;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            background-color: #fff;
        }

        input:focus {
            border-color: #3498db;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #27ae60;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
            }

            .main-content {
                padding: 20px;
            }

            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <h2 style="text-align:left;">Admin Panel</h2>
            <a href="labto_dashboard.php">Dashboard</a>
            <a href="labto_labRecord.php">Lab Record</a>
            <a href="labto_equipmentRecord.php">Equipment Record</a>
            <a href="../logout.php">Logout</a>
        </div>
        <div class="main-content">
            <div class="form-container">
                <h2>Add New Lab</h2>
                <form method="POST">
                    <label>Lab Name</label>
                    <input type="text" name="name" required>

                    <label>Lab Type</label>
                    <input type="text" name="type" required>

                    <label>Capacity</label>
                    <input type="number" name="capacity" required>

                    <label>Available Date</label>
                    <input type="date" name="date" required>

                    <label>Start Time</label>
                    <input type="time" name="start" required>

                    <label>End Time</label>
                    <input type="time" name="end" required>

                    <button type="submit">Add Lab</button>
                </form>
                <a href="labto_labRecord.php" class="back-link">← Back to Lab Record</a>
            </div>
        </div>
    </div>
</body>
</html>

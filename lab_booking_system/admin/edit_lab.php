<?php
include '../config.php';
session_start();
include 'header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

$labId = $_GET['id'] ?? null;
if (!$labId) {
    echo "Invalid lab ID.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $capacity = intval($_POST['capacity']);
    $date = $_POST['date'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    $stmt = $conn->prepare("UPDATE available_lab SET Name=?, Type=?, Capacity=?, Available_Date=?, Start_Time=?, End_Time=? WHERE Lab_ID=?");
    $stmt->bind_param("ssisssi", $name, $type, $capacity, $date, $start, $end, $labId);
    $stmt->execute();
    header("Location: labto_labRecord.php");
    exit();
}

$result = $conn->query("SELECT * FROM available_lab WHERE Lab_ID=$labId");
$lab = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Lab</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            color: #333;
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

        .top-right {
            text-align: right;
            margin-bottom: 10px;
        }

        .top-right a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .edit-form {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .edit-form h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 24px;
            text-align: center;
        }

        .edit-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #34495e;
        }

        .edit-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            background-color: #fff;
        }

        .edit-form input:focus {
            border-color: #3498db;
            outline: none;
        }

        .edit-form button {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-form button:hover {
            background-color: #2980b9;
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
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="labto_dashboard.php">Dashboard</a>
        <a href="labto_labRecord.php">Lab Record</a>
        <a href="labto_equipmentRecord.php">Equipment Record</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="main-content">
        <div class="top-right"><a href="logout.php">Logout</a></div>
        <div class="edit-form">
            <h2>Edit Lab Details</h2>
            <form method="POST">
                <label>Name</label>
                <input name="name" value="<?php echo htmlspecialchars($lab['Name']); ?>" required>

                <label>Type</label>
                <input name="type" value="<?php echo htmlspecialchars($lab['Type']); ?>" required>

                <label>Capacity</label>
                <input name="capacity" type="number" value="<?php echo $lab['Capacity']; ?>" required>

                <label>Date</label>
                <input name="date" type="date" value="<?php echo $lab['Available_Date']; ?>" required>

                <label>Start Time</label>
                <input name="start" type="time" value="<?php echo $lab['Start_Time']; ?>" required>

                <label>End Time</label>
                <input name="end" type="time" value="<?php echo $lab['End_Time']; ?>" required>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        background: #f4f6f8;
        color: #333;
    }

    img{
        max-width: 100%;
        height: 250px;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2, h3 {
        color: #2c3e50;
        margin-bottom: 20px;
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #ecf0f1;
    }

    .status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        display: inline-block;
    }

    .status.approved {
        background-color: #2ecc71;
        color: white;
    }

    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        table, th, td {
            font-size: 13px;
        }
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


<?php
include '../config.php';
session_start();
// Ensure only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

include 'header.php';
echo '<div class="wrapper">';
echo '<div class="sidebar">
        <h2>Student Panel</h2>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="student_lab.php">Reserve Lab</a>
        <a href="student_equipment.php">Book Equipment </a>
        <a href="../logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';
echo '<div class="top-right"><a href="logout.php">Logout</a></div>';
echo "<h2>Student Dashboard</h2>";
echo "<h3>Available Labs & Details</h3>";

// Get Student_ID for this user
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT Student_ID FROM student WHERE user_id='$user_id'");
$row = $res->fetch_assoc();
$student_id = $row['Student_ID'];

// Query: All approved lab schedules not booked by this student
$sql = "
SELECT 
    ls.Schedule_ID, ls.Date, ls.Start_Time, ls.End_Time, ls.Remaining_Capacity,
    l.Name AS LabName, l.Type, l.Capacity AS LabCapacity, l.Lab_ID
    FROM lab_schedule ls
    JOIN lab l ON ls.Lab_ID = l.Lab_ID
    WHERE ls.Status = 'pending'
    AND ls.Remaining_Capacity > 0
    AND ls.Schedule_ID NOT IN (
      SELECT Schedule_ID FROM lab_booking WHERE User_ID = '$student_id'
  )
ORDER BY ls.Date, ls.Start_Time
";
$result = $conn->query($sql);

// Display all labs from the lab table
echo "<h3>Lab Directory</h3>";
$lab_query = $conn->query("SELECT Lab_ID, Name, Type, Capacity, Lab_TO_ID FROM lab");

if ($lab_query->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>Lab ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Lab TO ID</th>
          </tr>";
    while ($lab = $lab_query->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($lab['Lab_ID']) . "</td>
                <td>" . htmlspecialchars($lab['Name']) . "</td>
                <td>" . htmlspecialchars($lab['Type']) . "</td>
                <td>" . htmlspecialchars($lab['Capacity']) . "</td>
                <td>" . htmlspecialchars($lab['Lab_TO_ID']) . "</td>
              </tr>";
    }
    echo "</table><br><br>";
} else {
    echo "<p>No labs found in the system.</p><br>";
}

$schedule_query = $conn->query("
SELECT 
    ls.Schedule_ID, ls.Date, ls.Start_Time, ls.End_Time,
    l.Name AS LabName, l.Type
FROM lab_schedule ls
JOIN lab l ON ls.Lab_ID = l.Lab_ID
WHERE ls.Status = 'approved'
AND ls.Remaining_Capacity > 0
AND ls.Schedule_ID NOT IN (
    SELECT Schedule_ID FROM lab_booking WHERE User_ID = '$student_id'
)
ORDER BY ls.Date, ls.Start_Time
");

if ($schedule_query->num_rows > 0) {
    echo "<label for='schedule_id'>Select a Schedule:</label><br><br>";
    echo "<select name='schedule_id' id='schedule_id' required style='width:100%; padding:10px; border-radius:6px;'>";
    while ($schedule = $schedule_query->fetch_assoc()) {
        $label = htmlspecialchars($schedule['LabName']) . " (" . htmlspecialchars($schedule['Type']) . ") on " .
                 htmlspecialchars($schedule['Date']) . " [" . htmlspecialchars($schedule['Start_Time']) . " - " .
                 htmlspecialchars($schedule['End_Time']) . "]";
        echo "<option value='" . $schedule['Schedule_ID'] . "'>$label</option>";
    }
    echo "</select><br><br>";
} else {
    echo "<p>No available schedules for booking.</p>";
}

echo "</div>";
echo "</body></html>";
?>

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

    .status.pending {
        background-color: #f39c12;
        color: white;
    }

    .status.approved {
        background-color: #2ecc71;
        color: white;
    }

    .status.rejected {
        background-color: #e74c3c;
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
// lecture_dashboard.php
session_start();

include '../config.php';

// Only allow lectures
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecture') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];


include 'header.php';
echo '<div class="wrapper">';
echo '<div class="sidebar">
        <h2>Lecturer Panel</h2>
        <a href="lecture_dashboard.php">Dashboard</a>
        <a href="lecturer_lab.php">Lab Reservation</a>
        <a href="lecturer_equipment.php">Equipment Booking</a>
        <a href="logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';

echo '<div class="top-right">';
echo "<strong>User ID: " . htmlspecialchars($userId) . "</strong> | ";

echo '<a href="../logout.php">Logout</a>';
echo '</div>';

echo "<h2>Lecture Dashboard</h2>";

/* 3. All Scheduled Lab Sessions */
echo "<h3>All Scheduled Lab Sessions</h3>";
$sessions = $conn->query("
    SELECT ls.Schedule_ID, l.Name AS LabName, ls.Date, ls.Start_Time, ls.End_Time, 
           ls.Remaining_Capacity, ls.Status, i.Name AS Instructor
    FROM lab_schedule ls
    JOIN lab l ON ls.Lab_ID = l.Lab_ID
    LEFT JOIN instructor i ON ls.Instructor_ID = i.Instructor_ID
    ORDER BY ls.Date DESC, ls.Start_Time DESC
");
if ($sessions && $sessions->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr>
        <th>Schedule ID</th><th>Lab</th><th>Date</th><th>Time</th>
        <th>Instructor</th><th>Capacity</th><th>Status</th>
    </tr>";
    while ($row = $sessions->fetch_assoc()) {
        echo "<tr>
            <td>{$row['Schedule_ID']}</td>
            <td>" . htmlspecialchars($row['LabName']) . "</td>
            <td>{$row['Date']}</td>
            <td>{$row['Start_Time']} - {$row['End_Time']}</td>
            <td>" . htmlspecialchars($row['Instructor']) . "</td>
            <td>{$row['Remaining_Capacity']}</td>
            <td>" . ucfirst($row['Status']) . "</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No scheduled sessions found.</p>";
}

/* 4. Your Bookings */
echo "<h3>Your Lab Bookings</h3>";
$bookings = $conn->prepare("
    SELECT lb.Booking_ID, s.username AS Student, lb.Status, l.Name AS LabName, 
           ls.Date, ls.Start_Time, ls.End_Time
    FROM lab_booking lb
    JOIN users s ON lb.User_ID = s.user_id
    JOIN lab_schedule ls ON lb.Schedule_ID = ls.Schedule_ID
    JOIN lab l ON ls.Lab_ID = l.Lab_ID
    WHERE lb.User_ID = ?
    ORDER BY lb.Booking_ID DESC
");
$bookings->bind_param("s", $userId);
$bookings->execute();
$result = $bookings->get_result();



if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr>
        <th>Booking ID</th><th>Student</th><th>Lab</th>
        <th>Date</th><th>Time</th><th>Status</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['Booking_ID']}</td>
            <td>" . htmlspecialchars($row['Student']) . "</td>
            <td>" . htmlspecialchars($row['LabName']) . "</td>
            <td>{$row['Date']}</td>
            <td>{$row['Start_Time']} - {$row['End_Time']}</td>
            <td>" . ucfirst($row['Status']) . "</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No bookings found.</p>";
}

/* 5. Your Equipment Bookings */
echo "<h3>All Equipment Bookings</h3>";

$allBookings = $conn->prepare("
    SELECT eb.Booking_ID, u.username AS User, le.Name AS Equipment, eb.Quantity, eb.Booking_Date, eb.Booking_Time, eb.Status
    FROM equipment_booking eb
    JOIN lab_equipment le ON eb.Equipment_ID = le.Equipment_ID
    JOIN users u ON eb.User_ID = u.user_id
    WHERE eb.User_ID = ?
    ORDER BY eb.Booking_Date DESC, eb.Booking_Time DESC
");
$allBookings->bind_param("s", $userId); // Use "s" for string
$allBookings->execute();
$result = $allBookings->get_result();


if ($result && $result->num_rows > 0) {
    echo "<table><tr>
        <th>Booking ID</th><th>User</th><th>Equipment</th><th>Quantity</th>
        <th>Date</th><th>Time</th><th>Status</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        $statusClass = strtolower($row['Status']);
        echo "<tr>
            <td>{$row['Booking_ID']}</td>
            <td>" . htmlspecialchars($row['User']) . "</td>
            <td>" . htmlspecialchars($row['Equipment']) . "</td>
            <td>{$row['Quantity']}</td>
            <td>{$row['Booking_Date']}</td>
            <td>{$row['Booking_Time']}</td>
            <td><span class='status {$statusClass}'>" . ucfirst($row['Status']) . "</span></td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No equipment bookings found for your account.</p>";
}





echo '</div>';
echo "</body></html>";
?>

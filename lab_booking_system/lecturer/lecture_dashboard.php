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
        <a href="lecturer_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';

echo '<div class="top-right">';
echo "<strong>User ID: " . htmlspecialchars($userId) . "</strong> | ";

echo '<a href="../logout.php">Logout</a>';
echo '</div>';

echo "<h2>Lecture Dashboard</h2>";

/* 6. Available Labs */
echo "<h3>Available Labs</h3>";
$availableLabs = $conn->query("
    SELECT Lab_ID, Name, Type, Capacity, Available_Date, Start_Time, End_Time
    FROM available_lab
    ORDER BY Available_Date ASC, Start_Time ASC
");

if ($availableLabs && $availableLabs->num_rows > 0) {
    echo "<table><tr>
        <th>Lab ID</th><th>Name</th><th>Type</th><th>Capacity</th>
        <th>Date</th><th>Time</th>
    </tr>";
    while ($row = $availableLabs->fetch_assoc()) {
        echo "<tr>
            <td>{$row['Lab_ID']}</td>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>{$row['Type']}</td>
            <td>{$row['Capacity']}</td>
            <td>{$row['Available_Date']}</td>
            <td>{$row['Start_Time']} - {$row['End_Time']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No available labs at the moment.</p>";
}

/* 7. Booked Labs */
echo "<h3>Your Booked Labs</h3>";
$bookedLabs = $conn->prepare("
    SELECT Booking_ID, Lab_ID, Name, Type, Capacity, Booking_Date, Start_Time, End_Time, Status
    FROM booked_lab
    WHERE User_ID = ?
    ORDER BY Booking_Date DESC, Start_Time DESC
");
$bookedLabs->bind_param("s", $userId);
$bookedLabs->execute();
$result = $bookedLabs->get_result();

if ($result && $result->num_rows > 0) {
    echo "<table><tr>
        <th>Booking ID</th><th>Lab ID</th><th>Name</th><th>Type</th><th>Capacity</th>
        <th>Date</th><th>Time</th><th>Status</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        $statusClass = strtolower($row['Status']);
        echo "<tr>
            <td>{$row['Booking_ID']}</td>
            <td>{$row['Lab_ID']}</td>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>{$row['Type']}</td>
            <td>{$row['Capacity']}</td>
            <td>{$row['Booking_Date']}</td>
            <td>{$row['Start_Time']} - {$row['End_Time']}</td>
            <td><span class='status {$statusClass}'>" . ucfirst($row['Status']) . "</span></td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>You haven’t booked any labs yet.</p>";
}

/* 8. Your Booked Equipment */
echo "<h3>Your Booked Equipment</h3>";
$bookedEquip = $conn->prepare("
    SELECT Booking_ID, Equipment_ID, Name, Type, Quantity, Booking_Date, Status
    FROM booked_equipment
    WHERE User_ID = ?
    ORDER BY Booking_Date DESC
");
$bookedEquip->bind_param("s", $userId);
$bookedEquip->execute();
$result = $bookedEquip->get_result();

if ($result && $result->num_rows > 0) {
    echo "<table><tr>
        <th>Booking ID</th><th>Equipment ID</th><th>Name</th><th>Type</th><th>Quantity</th>
        <th>Date</th><th>Status</th>
    </tr>";
    while ($row = $result->fetch_assoc()) {
        $statusClass = strtolower($row['Status']);
        echo "<tr>
            <td>{$row['Booking_ID']}</td>
            <td>{$row['Equipment_ID']}</td>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>{$row['Type']}</td>
            <td>{$row['Quantity']}</td>
            <td>{$row['Booking_Date']}</td>
            <td><span class='status {$statusClass}'>" . ucfirst($row['Status']) . "</span></td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>You haven’t booked any equipment yet.</p>";
}


echo '</div>';
echo "</body></html>";
?>

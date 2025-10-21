
<?php
include '../config.php';
session_start();
// Ensure only admin can access = labto == admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}
?>
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

.dashboard-boxes {
    display: flex;
    gap: 20px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.box {
    flex: 1;
    min-width: 280px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 25px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    transition: transform 0.2s ease;
}

.box:hover {
    transform: translateY(-4px);
}

.box h4 {
    font-size: 18px;
    color: #2c3e50;
    margin-bottom: 10px;
}

.box .count {
    font-size: 36px;
    font-weight: bold;
    color: #3498db;
}

.box .icon {
    font-size: 24px;
    color: #bdc3c7;
    margin-bottom: 10px;
}
.action-btn {
    display: inline-block;
    padding: 5px 5px;
    margin-right: 6px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.action-edit {
    background-color: #3498db;
    color: white;
}

.action-edit:hover {
    background-color: #2980b9;
}

.action-delete {
    background-color: #e74c3c;
    color: white;
}

.action-delete:hover {
    background-color: #c0392b;
}

.action-approve {
    background-color: #2ecc71;
    color: white;
}

.action-approve:hover {
    background-color: #27ae60;
}

.action-reject {
    background-color: #e74c3c;
    color: white;
}

.action-reject:hover {
    background-color: #c0392b;
}

</style>

<?php
include 'header.php';
echo '<div class="wrapper">';
echo '<div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="labto_dashboard.php">Dashboard</a>
        <a href="labto_labRecord.php">Lab Record</a>
        <a href="labto_equipmentRecord.php">Equipment Record</a>
        <a href="labto_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';
echo '<div class="top-right"><a href="logout.php">Logout</a></div>';
echo "<h2>Lab Record</h2>";

// start here
/* 1. Available Labs */
echo "<h3>Available Labs</h3>";
$availableDate = $_GET['available_date'] ?? '';
if ($availableDate) {
    $availableLabs = $conn->query("SELECT * FROM available_lab WHERE Available_Date = '$availableDate' ORDER BY Available_Date ASC, Start_Time ASC");
} else {
    $availableLabs = $conn->query("SELECT * FROM available_lab ORDER BY Available_Date ASC, Start_Time ASC");
}


//filter
echo '<form method="GET" style="margin-bottom: 20px;">
    <label for="available_date">Filter by Date:</label>
    <input type="date" name="available_date" id="available_date" value="' . ($_GET['available_date'] ?? '') . '" style="padding: 6px; margin-left: 10px; margin-right: 10px;">
    <button type="submit" class="action-btn action-edit">Apply</button>
</form>';


if ($availableLabs && $availableLabs->num_rows > 0) {
    echo "<table><tr>
        <th>Lab ID</th><th>Name</th><th>Type</th><th>Capacity</th>
        <th>Date</th><th>Time</th><th>Actions</th>
    </tr>";
    while ($row = $availableLabs->fetch_assoc()) {
        echo "<tr>
            <td>{$row['Lab_ID']}</td>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>{$row['Type']}</td>
            <td>{$row['Capacity']}</td>
            <td>{$row['Available_Date']}</td>
            <td>{$row['Start_Time']} - {$row['End_Time']}</td>
<td>
    <a href='edit_lab.php?id={$row['Lab_ID']}' class='action-btn action-edit'>Edit</a>
    <a href='delete_lab.php?id={$row['Lab_ID']}' class='action-btn action-delete' onclick=\"return confirm('Delete this lab?')\">Delete</a>
</td>

        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No available labs found.</p>";
}

echo '<div style="margin: 20px 0;">
    <a href="add_lab.php" style="
        display: inline-block;
        padding: 8px 6px;
        background-color: #2ecc71;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    ">+ Add New Lab</a>
</div>';


/* 2. Booked Labs */
echo "<h3>Booked Lab Requests</h3>";
$bookedDate = $_GET['booked_date'] ?? '';
if ($bookedDate) {
    $bookedLabs = $conn->query("SELECT * FROM booked_lab WHERE Booking_Date = '$bookedDate' ORDER BY Booking_Date DESC, Start_Time DESC");
} else {
    $bookedLabs = $conn->query("SELECT * FROM booked_lab ORDER BY Booking_Date DESC, Start_Time DESC");
}


echo '<form method="GET" style="margin-bottom: 20px;">
    <label for="booked_date">Filter by Date:</label>
    <input type="date" name="booked_date" id="booked_date" value="' . ($_GET['booked_date'] ?? '') . '" style="padding: 6px; margin-left: 10px; margin-right: 10px;">
    <button type="submit" class="action-btn action-edit">Apply</button>
</form>';



if ($bookedLabs && $bookedLabs->num_rows > 0) {
    echo "<table><tr>
        <th>Booking ID</th><th>Lab Name</th><th>Type</th><th>User ID</th>
        <th>Date</th><th>Time</th><th>Status</th><th>Profile</th><th>Actions</th>
    </tr>";
    while ($row = $bookedLabs->fetch_assoc()) {
        $statusClass = strtolower($row['Status']);
        echo "<tr>
            <td>{$row['Booking_ID']}</td>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>{$row['Type']}</td>
            <td>{$row['User_ID']}</td>
            <td>{$row['Booking_Date']}</td>
            <td>{$row['Start_Time']} - {$row['End_Time']}</td>
            <td><span class='status {$statusClass}'>" . ucfirst($row['Status']) . "</span></td>
            <td>
            <a href='view_user.php?id={$row['User_ID']}' class='action-btn action-edit'>View</a>
            </td>";

echo "<td>";
if ($statusClass === 'pending') {
    echo "<a href='approve_lab.php?id={$row['Booking_ID']}' class='action-btn action-approve'>Approve</a>
          <a href='reject_lab.php?id={$row['Booking_ID']}' class='action-btn action-reject'>Reject</a>";
} else {
    echo "<span style='color: #888; font-weight: 500;'>No actions available</span>";
}
echo "</td>";

      echo"</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No lab booking requests found.</p>";
}

echo "</div>";
echo "</body></html>";
?>

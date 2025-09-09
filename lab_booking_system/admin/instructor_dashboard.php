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

    form label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
    }

    form input, form select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    button {
        margin-top: 20px;
        padding: 10px 20px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background: #2980b9;
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

        button {
            width: 100%;
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
include 'config.php';

// Ensure only instructors can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

include 'header.php';
echo '<div class="wrapper">';
echo '<div class="sidebar">
        <h2>Instructor Panel</h2>
        <a href="instructor_dashboard.php">Dashboard</a>
        <a href="schedule_lab.php">Schedule Lab</a>
        <a href="view_labs.php">My Labs</a>
        <a href="logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';

echo '<div class="top-right"><a href="logout.php">Logout</a></div>';
echo "<h2>Instructor Dashboard</h2>";

// Get Instructor_ID for the logged-in user
$user_id = $_SESSION['user_id'];
$instructor_res = $conn->query("SELECT Instructor_ID FROM instructor WHERE user_id='$user_id'");
$instructor_row = $instructor_res->fetch_assoc();
$instructor_id = $instructor_row['Instructor_ID'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_lab'])) {
    $lab_id = (int)$_POST['lab_id'];
    $date = $conn->real_escape_string($_POST['date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $capacity = (int)$_POST['capacity'];

    // Find Lab_TO_ID for the selected lab
    $lab_to_res = $conn->query("SELECT Lab_TO_ID FROM lab WHERE Lab_ID=$lab_id");
    $lab_to_row = $lab_to_res->fetch_assoc();
    $lab_to_id = $lab_to_row['Lab_TO_ID'];

    // Insert into lab_schedule
    $sql = "INSERT INTO lab_schedule (Date, Start_Time, End_Time, Lab_ID, Remaining_Capacity, Status, Instructor_ID, Lab_TO_ID)
            VALUES ('$date', '$start_time', '$end_time', $lab_id, $capacity, 'pending', $instructor_id, $lab_to_id)";
    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Lab scheduled successfully and is pending approval by Lab TO.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}

// Get list of labs for dropdown
$labs_res = $conn->query("SELECT Lab_ID, Name, Capacity FROM lab");
?>

<h3>Schedule a New Lab Session</h3>
<form method="post" action="">
    <label>Lab:</label>
    <select name="lab_id" required>
        <option value="">Select Lab</option>
        <?php while ($lab = $labs_res->fetch_assoc()): ?>
            <option value="<?= $lab['Lab_ID'] ?>">
                <?= htmlspecialchars($lab['Name']) ?> (Capacity: <?= $lab['Capacity'] ?>)
            </option>
        <?php endwhile; ?>
    </select><br><br>
    <label>Date:</label>
    <input type="date" name="date" required><br><br>
    <label>Start Time:</label>
    <input type="time" name="start_time" required><br><br>
    <label>End Time:</label>
    <input type="time" name="end_time" required><br><br>
    <label>Initial Capacity:</label>
    <input type="number" name="capacity" min="1" required><br><br>
    <button type="submit" name="schedule_lab">Schedule Lab</button>
</form>

<hr>

<h3>Your Scheduled Labs</h3>
<?php
// Show all schedules created by this instructor
$schedules = $conn->query("SELECT ls.*, l.Name as LabName FROM lab_schedule ls
    JOIN lab l ON ls.Lab_ID = l.Lab_ID
    WHERE ls.Instructor_ID = $instructor_id
    ORDER BY ls.Date DESC, ls.Start_Time DESC");
if ($schedules->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr>
        <th>Lab</th><th>Date</th><th>Time</th><th>Capacity</th><th>Status</th></tr>";
    while ($row = $schedules->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['LabName']) . "</td>
            <td>" . $row['Date'] . "</td>
            <td>" . $row['Start_Time'] . " - " . $row['End_Time'] . "</td>
            <td>" . $row['Remaining_Capacity'] . "</td>
            <td>" . ucfirst($row['Status']) . "</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No scheduled labs yet.</p>";
}
echo '</div>'; // end main-content
echo '</div>'; // end wrapper
echo '</div>';
echo "</body></html>";
?>




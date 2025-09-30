<?php
include '../config.php';
session_start();
// Ensure only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
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

.filter-form label {
    font-weight: 600;
    margin-right: 10px;
}

.filter-form input[type="date"] {
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.filter-form button {
    padding: 6px 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
}

.filter-form button:hover {
    background-color: #2980b9;
}


</style>

<?php
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

/* 2. Available Labs to Book */
echo "<h3>Available Labs</h3>";

echo '<form method="GET" class="filter-form" style="margin-bottom: 20px;">
    <label for="filter_date">Filter by Date:</label>
    <input type="date" name="filter_date" id="filter_date" value="' . htmlspecialchars($_GET['filter_date'] ?? '') . '">
    <button type="submit">Apply</button>
</form>';



$filterDate = $_GET['filter_date'] ?? '';
if ($filterDate) {
    $availableLabs = $conn->query("
        SELECT Lab_ID, Name, Type, Capacity, Available_Date, Start_Time, End_Time
        FROM available_lab
        WHERE Available_Date = '$filterDate'
        ORDER BY Available_Date ASC, Start_Time ASC
    ");
} else {
    $availableLabs = $conn->query("
        SELECT Lab_ID, Name, Type, Capacity, Available_Date, Start_Time, End_Time
        FROM available_lab
        ORDER BY Available_Date ASC, Start_Time ASC
    ");
}



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

/* 3. Booking Form */
echo '<div class="container" style="margin-top: 40px; max-width: 700px;">
    <h3 style="color:#2c3e50;">Book a Lab</h3>
    <form action="student_booklab.php" method="POST" style="display: grid; gap: 20px;">';

    echo '<label for="lab_id">Select Lab</label>';
    echo '<select name="lab_id" id="lab_id" required>
            <option value="" disabled selected>Select lab</option>';

    $labs = $conn->query("
        SELECT Lab_ID, Name, Type, Capacity, Available_Date, Start_Time, End_Time
        FROM available_lab
        ORDER BY Available_Date ASC, Start_Time ASC
    ");
    if ($labs && $labs->num_rows > 0) {
        while ($lab = $labs->fetch_assoc()) {
            $label = htmlspecialchars($lab['Name']) . " (" . $lab['Type'] . ", " . $lab['Available_Date'] . " " . $lab['Start_Time'] . " - " . $lab['End_Time'] . ")";
            echo "<option value='{$lab['Lab_ID']}'>$label</option>";
        }
    } else {
        echo '<option disabled>No labs available</option>';
    }
    echo '</select>';

    echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['user_id']) . '">';

    echo '<button type="submit" style="
        padding: 14px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    ">Submit Booking</button>';

echo '</form></div>';

echo "</div>";
echo "<script>";
echo "
document.querySelector('form[action='student_booklab.php']')?.addEventListener('submit', function(e) {

    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('student_booklab.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === 'success') {
            alert('✅ Booking successful!');
            setTimeout(() => {
                location.reload(); // refresh the page after 1.5 seconds
            }, 1500);
        } else {
            alert('❌ ' + response);
        }
    })
    .catch(() => {
        alert('❌ Something went wrong. Please try again.');
    });
});
";
echo "</script>";
echo "</body></html>";
?>
